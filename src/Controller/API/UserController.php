<?php

namespace App\Controller\API;

use App\Middleware\Auth;
use App\Utils\DatabaseConnection;
use App\Utils\QueryParam;
use App\Utils\Response\NormalizedJsonResponse;
use App\Utils\Response\UserInfoResponse;
use App\Utils\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users', name: 'users_')]
class UserController extends AbstractController
{
    #[Route('/{userId<\d+>}', name: 'index', methods: ['GET'])]
    public function getUserInfo(string $userId): JsonResponse
    {
        return new UserInfoResponse($userId);
    }

    #[Route(['', '/', '/signup'], name: 'register', methods: ['POST'])]
    public function registerUser(Request $request): JsonResponse
    {
        return AuthController::_internal_register($request);
    }

    #[Route('/{userId<\d+>}', name: 'update', methods: ['PUT'])]
    public function updateUser(Request $request, string $userId): JsonResponse
    {
        $user = Auth::assert($request);
        if ($user['id'] != $userId) {
            throw new AccessDeniedHttpException('You cannot change information of another user.');
        }
        $data = json_decode($request->getContent(), true);

        $availableFields = Validator::validateUserUpdate_AllAreOptional($data);
        $updates = [];
        if (!empty($availableFields)) {
            if (in_array('password', $availableFields)) {
                $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
                $updates['password_hash'] = $passwordHash;
                // Remove 'password_hash' from available fields.
                unset($availableFields[array_search('password', $availableFields)]);
            }
            // Iterate over the available fields (except 'password')
            foreach ($availableFields as $field) {
                $updates[$field] = $data[$field];
            }
            // Run SQL update
            $stmt = DatabaseConnection::prepare('UPDATE users SET ' . implode(', ',
                array_map(
                    fn ($field) => "$field = :$field",
                    array_keys($updates)
                )
            ) . ' WHERE id = :userId');
            foreach ($updates as $field => &$value) {
                $stmt->bindParam(":$field", $value, QueryParam::STR);
            }
            $stmt->bindParam(':userId', $userId, QueryParam::STR);
            $stmt->execute();
        }

        // Regardless of whether there's an actual update or not,
        // we still return the user information.
        return new UserInfoResponse($userId);
    }

    #[Route('/{userId<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function deleteUser(Request $request, string $userId): JsonResponse
    {
        $user = Auth::assert($request);
        if ($user['id'] != $userId) {
            if (!$user['is_admin']) {
                throw new AccessDeniedHttpException('You cannot delete another user if you are not an admin.');
            }
        }

        $stmt = DatabaseConnection::prepare('DELETE FROM users WHERE id = :userId');
        $stmt->bindParam(':userId', $userId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }
}
