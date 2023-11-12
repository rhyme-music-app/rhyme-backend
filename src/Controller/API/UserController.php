<?php

namespace App\Controller\API;

use App\Middleware\Auth;
use App\Utils\DatabaseConnection;
use App\Utils\QueryParam;
use App\Utils\Response\ListResponse;
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
    /**
     * Route 1
     */
    #[Route(['', '/'], name: 'index', methods: ['GET'])]
    public function indexUsers(): JsonResponse
    {
        return ListResponse::selectAllFromOneTable('users', [
            'id', 'email', 'name', 'is_admin', 'deleted'
        ]);
    }

    /**
     * Route 2
     */
    #[Route('/{userId<\d+>}', name: 'get', methods: ['GET'])]
    public function getUserInfo(string $userId): JsonResponse
    {
        return new UserInfoResponse($userId);
    }

    /**
     * Route 3
     */
    #[Route(['', '/', '/signup'], name: 'register', methods: ['POST'])]
    public function registerUser(Request $request): JsonResponse
    {
        return AuthController::_internal_register($request);
    }

    /**
     * Route 4
     */
    #[Route('/{userId<\d+>}', name: 'update', methods: ['PUT'])]
    public function updateUser(Request $request, string $userId): JsonResponse
    {
        $user = Auth::assert($request, 'You cannot change information of another user.');
        $data = json_decode($request->getContent(), true);

        $availableFields = Validator::validateUserUpdate_AllAreOptional($data);
        if (!empty($availableFields)) {
            $updates = [];
            if (in_array('password', $availableFields)) {
                $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
                $updates['password_hash'] = [
                    'value' => $passwordHash,
                    'type' => QueryParam::STR,
                ];
                // Remove 'password_hash' from available fields.
                unset($availableFields[array_search('password', $availableFields)]);
            }
            // Iterate over the available fields (except 'password')
            foreach ($availableFields as $field) {
                $updates[$field] = [
                    'value' => $data[$field],
                    'type' => QueryParam::STR,
                ];
            }
            // Run SQL update
            $stmt = DatabaseConnection::prepareUpdateAndBind('users', $userId, $updates);
            $stmt->execute();
        }

        // Regardless of whether there's an actual update or not,
        // we still return the user information.
        return new UserInfoResponse($userId);
    }

    /**
     * Route 5
     */
    #[Route('/{userId<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function deleteUser(Request $request, string $userId): JsonResponse
    {
        $user = Auth::assert($request);
        if ($user['id'] != $userId) {
            if (!$user['is_admin']) {
                throw new AccessDeniedHttpException('You cannot delete another user if you are not an admin.');
            }
        }

        $stmt = DatabaseConnection::prepare('DELETE FROM users WHERE id = :userId;');
        $stmt->bindParam(':userId', $userId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }
}
