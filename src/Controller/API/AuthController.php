<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Utils\DatabaseConnection;
use App\Utils\QueryParam;
use App\Utils\Response\UserInfoResponse;
use App\Utils\Validator;

#[Route('/auth', name: 'auth_')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        Validator::validateArray_AllMustExist($data, [
            'email' => 'users.email',
            'password' => 'users.password',
            'name' => 'users.name',
        ]);
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
}
