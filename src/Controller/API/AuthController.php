<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Middleware\Auth;

use App\Utils\DatabaseConnection;
use App\Utils\QueryFetch;
use App\Utils\QueryParam;
use App\Utils\Response\NormalizedJsonResponse;
use App\Utils\Response\UserInfoResponse;
use App\Utils\Response\UserTokenResponse;
use App\Utils\Validator;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

#[Route('/auth', name: 'auth_')]
class AuthController extends AbstractController
{
    public static function _internal_register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        Validator::validateUserUpdate_allMustPresent($data);
        $email = $data['email'];
        $password = $data['password'];
        $name = $data['name'];

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        unset($password);

        $stmt = DatabaseConnection::prepare(
            'INSERT INTO users (email, name, password_hash)
            VALUES (:email, :name, :password_hash);'
        );
        $stmt->bindParam(':email', $email, QueryParam::STR);
        $stmt->bindParam(':name', $name, QueryParam::STR);
        $stmt->bindParam(':password_hash', $passwordHash, QueryParam::STR);
        $stmt->execute();

        return new UserInfoResponse(DatabaseConnection::lastInsertId());
    }

    /**
     * Returns the user array.
     */
    public static function _internal_validate_login(array $data): array
    {
        Validator::validateArray_AllMustPresent($data, [
            'email' => 'users.email',
            'password' => 'users.password',
        ]);
        $email = $data['email'];
        $password = $data['password'];

        $stmt = DatabaseConnection::prepare('SELECT id, email, name, password_hash, is_admin, deleted FROM users WHERE email = :email');
        $stmt->bindParam(':email', $email, QueryParam::STR);
        $stmt->execute();

        $user = $stmt->fetch(QueryFetch::ASSOC);
        if (!$user) {
            throw new UnauthorizedHttpException('None', 'Wrong email or password.');
        }
        
        $passwordHash = $user['password_hash'];
        if (!password_verify($password, $passwordHash)) {
            throw new UnauthorizedHttpException('None', 'Wrong email or password.');
        }

        return $user;
    }

    public static function _internal_login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return new UserTokenResponse(self::_internal_validate_login($data)['id']);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        return self::_internal_register($request);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        return self::_internal_login($request);
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        Auth::assertAndLogout($request);

        return new NormalizedJsonResponse([], 200);
    }
}
