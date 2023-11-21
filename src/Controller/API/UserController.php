<?php

namespace App\Controller\API;

use App\Middleware\Auth;
use App\Middleware\FavoriteAccess;
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
            'id', 'email', 'image_link', 'name', 'is_admin', 'deleted'
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

    /**
     * Route 6
     */
    #[Route('/{userId<\d+>}/favorite/playlists', name: 'get_favorite_playlists', methods: ['GET'])]
    public function getFavoritePlaylists(Request $request, string $userId): JsonResponse
    {
        $user = Auth::assert($request);
        FavoriteAccess::assertViewer($userId, $user);

        $playlistFields = [
            'id', 'name', 'image_link', 'owned_by', 'is_public',
            'added_at', 'updated_at'
        ];
        $stmt = DatabaseConnection::prepare('SELECT
            ' . implode(', ', $playlistFields) . '
            FROM
                playlists p
            INNER JOIN favorite_playlist_user pivot
                ON pivot.favorite_playlist_id = p.id
            WHERE
                pivot.user_id = :userId;'
        );
        $stmt->bindParam(':userId', $userId, QueryParam::STR);
        return ListResponse::withCustomQuery($stmt, $playlistFields);
    }

    /**
     * Route 7
     */
    #[Route('/{userId<\d+>}/favorite/playlists/{playlistId<\d+>}', name: 'add_favorite_playlist', methods: ['POST'])]
    public function markPlaylistAsFavorite(Request $request, string $userId, string $playlistId): JsonResponse
    {
        $user = Auth::assert($request);
        FavoriteAccess::assertEditor($userId, $user);

        $stmt = DatabaseConnection::prepare('INSERT INTO favorite_playlist_user
            (favorite_playlist_id, user_id)
            VALUES
            (:playlistId, :userId);'
        );
        $stmt->bindParam(':playlistId', $playlistId, QueryParam::STR);
        $stmt->bindParam(':userId', $userId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }

    /**
     * Route 8
     */
    #[Route('/{userId<\d+>}/favorite/playlists/{playlistId<\d+>}', name: 'remove_favorite_playlist', methods: ['DELETE'])]
    public function unmarkPlaylistAsFavorite(Request $request, string $userId, string $playlistId): JsonResponse
    {
        $user = Auth::assert($request);
        FavoriteAccess::assertEditor($userId, $user);

        $stmt = DatabaseConnection::prepare('DELETE FROM favorite_playlist_user
            WHERE
                favorite_playlist_id = :playlistId
                AND user_id = :userId;'
        );
        $stmt->bindParam(':playlistId', $playlistId, QueryParam::STR);
        $stmt->bindParam(':userId', $userId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }

    /**
     * Route 9
     */
    #[Route('/{userId<\d+>}/favorite/songs', name: 'get_favorite_songs', methods: ['GET'])]
    public function getFavoriteSongs(Request $request, string $userId): JsonResponse
    {
        $user = Auth::assert($request);
        FavoriteAccess::assertViewer($userId, $user);

        $songFields = [
            'id', 'name', 'audio_link', 'image_link',
            'added_at', 'updated_at',
            'added_by', 'updated_by',
            'streams'
        ];
        $stmt = DatabaseConnection::prepare('SELECT
            ' . implode(', ', $songFields) . '
            FROM
                songs s
            INNER JOIN favorite_song_user pivot
                ON pivot.favorite_song_id = s.id
            WHERE
                pivot.user_id = :userId;'
        );
        $stmt->bindParam(':userId', $userId, QueryParam::STR);
        return ListResponse::withCustomQuery($stmt, $songFields);
    }

    /**
     * Route 10
     */
    #[Route('/{userId<\d+>}/favorite/songs/{songId<\d+>}', name: 'add_favorite_song', methods: ['POST'])]
    public function markSongAsFavorite(Request $request, string $userId, string $songId): JsonResponse
    {
        $user = Auth::assert($request);
        FavoriteAccess::assertEditor($userId, $user);

        $stmt = DatabaseConnection::prepare('INSERT INTO favorite_song_user
            (favorite_song_id, user_id)
            VALUES
            (:songId, :userId);'
        );
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        $stmt->bindParam(':userId', $userId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }

    /**
     * Route 11
     */
    #[Route('/{userId<\d+>}/favorite/songs/{songId<\d+>}', name: 'remove_favorite_song', methods: ['DELETE'])]
    public function unmarkSongAsFavorite(Request $request, string $userId, string $songId): JsonResponse
    {
        $user = Auth::assert($request);
        FavoriteAccess::assertEditor($userId, $user);

        $stmt = DatabaseConnection::prepare('DELETE FROM favorite_song_user
            WHERE
                favorite_song_id = :songId
                AND user_id = :userId;'
        );
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        $stmt->bindParam(':userId', $userId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }

    /**
     * Route 12
     */
    #[Route('/{userId<\d+>}/own/playlists', name: 'get_own_playlists', methods: ['GET'])]
    public function getOwnPlaylists(Request $request, string $userId): JsonResponse
    {
        $user = Auth::getUserNoException($request);
        $playlistFields = [
            'id', 'name', 'image_link', 'owned_by', 'is_public',
            'added_at', 'updated_at'
        ];

        if ($user && $user['id'] == $userId) {
            return ListResponse::selectFromOneTableWithCommandTail(
                'playlists',
                $playlistFields,
                "WHERE owned_by = $userId"
            );
        } else {
            return ListResponse::selectFromOneTableWithCommandTail(
                'playlists',
                $playlistFields,
                "WHERE owned_by = $userId AND is_public = TRUE"
            );
        }
    }
}
