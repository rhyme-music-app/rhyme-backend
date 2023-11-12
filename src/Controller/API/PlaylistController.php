<?php

namespace App\Controller\API;

use App\Middleware\Auth;
use App\Middleware\PlaylistAccess;
use App\Utils\DatabaseConnection;
use App\Utils\QueryParam;
use App\Utils\Response\ListResponse;
use App\Utils\Response\NormalizedJsonResponse;
use App\Utils\Response\PlaylistInfoResponse;
use App\Utils\Validator;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('playlists', name: 'playlists_')]
class PlaylistController extends AbstractController
{
    /**
     * Route 1
     */
    #[Route(['', '/'], name: 'index', methods: ['GET'])]
    public function indexPlaylists(Request $request): JsonResponse
    {
        $user = Auth::getUserNoException($request);

        return ListResponse::selectFromOneTableWithCommandTail('playlists', [
            'id', 'name', 'owned_by', 'is_public',
            'added_at', 'updated_at'
        ], (
            $user === null
            ? 'WHERE is_public = TRUE'
            : "WHERE is_public = TRUE OR owned_by = " . $user['id']
        ));
    }

    /**
     * Route 2
     */
    #[Route('/{playlistId<\d+>}', name: 'get', methods: ['GET'])]
    public function getPlaylist(Request $request, string $playlistId): JsonResponse
    {
        $user = Auth::getUserNoException($request);

        return new PlaylistInfoResponse($playlistId, $user);
    }

    /**
     * Route 3
     */
    #[Route(['', '/'], name: 'add', methods: ['POST'])]
    public function addPlaylist(Request $request): JsonResponse
    {
        $user = Auth::assert($request);
        $userId = $user['id'];
        $data = json_decode($request->getContent(), true);
        Validator::validatePlaylistUpdate_AllMustPresent($data);

        $stmt = DatabaseConnection::prepare('INSERT INTO playlists
        (name, owned_by, is_public, added_at, updated_at)
        VALUES
        (:name, :owned_by, :is_public, :added_at, :updated_at);');

        $now = new DateTime();
        $stmt->bindParam(':name', $data['name'], QueryParam::STR);
        $stmt->bindParam(':owned_by', $userId, QueryParam::STR);
        $stmt->bindParam(':is_public', $data['is_public'], QueryParam::BOOL);
        $stmt->bindParam(':added_at', $now, QueryParam::DATETIME);
        $stmt->bindParam(':updated_at', $now, QueryParam::DATETIME);
        $stmt->execute();

        return new PlaylistInfoResponse(DatabaseConnection::lastInsertId(), $user);
    }

    /**
     * Route 4
     */
    #[Route('/{playlistId<\d+>}', name: 'update', methods: ['PUT'])]
    public function updatePlaylist(Request $request, string $playlistId): JsonResponse
    {
        $user = Auth::assert($request);
        $playlist = PlaylistAccess::assertEditor($playlistId, $user);
        $data = json_decode($request->getContent(), true);

        $availableFields = Validator::validatePlaylistUpdate_AllAreOptional($data);

        if (!empty($availableFields)) {
            $updates = [];
            foreach ($availableFields as $field) {
                $updates[$field] = [
                    'value' => $data[$field],
                    'type' => ($field == 'is_public' ? QueryParam::BOOL : QueryParam::STR),
                ];
            }
            $now = new DateTime();
            $updates['updated_at'] = [
                'value' => $now,
                'type' => QueryParam::DATETIME,
            ];
            $stmt = DatabaseConnection::prepareUpdateAndBind('playlists', $playlistId, $updates);
            $stmt->execute();
        }
        
        return new PlaylistInfoResponse($playlistId, $user);
    }

    /**
     * Route 5
     */
    #[Route('/{playlistId<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function deletePlaylist(Request $request, string $playlistId): JsonResponse
    {
        $user = Auth::assert($request);
        $playlist = PlaylistAccess::assertEditor($playlistId, $user, 'You don\'t have the right to delete this playlist.');

        $stmt = DatabaseConnection::prepare('DELETE FROM playlists WHERE id = :playlistId;');
        $stmt->bindParam(':playlistId', $playlistId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }

    /**
     * Route 6
     */
    #[Route('/{playlistId<\d+>}/songs', name: 'get_songs', methods: ['GET'])]
    public function getPlaylistSongs(Request $request, string $playlistId): JsonResponse
    {
        $user = Auth::getUserNoException($request);
        $playlist = PlaylistAccess::assertViewer($playlistId, $user);

        $songFields = [
            'id', 'name', 'audio_link',
            'added_at', 'updated_at',
            'added_by', 'updated_by',
            'streams'
        ];
        $stmt = DatabaseConnection::prepare('SELECT
            ' . implode(', ', $songFields) . '
            FROM
                songs s
            INNER JOIN playlist_song pivot
                ON pivot.song_id = s.id
            WHERE
                pivot.playlist_id = :playlistId;'
        );
        $stmt->bindParam(':playlistId', $playlistId, QueryParam::STR);
        return ListResponse::withCustomQuery($stmt, $songFields);
    }

    /**
     * Route 7
     */
    #[Route('/{playlistId<\d+>}/songs/{songId<\d+>}', name: 'add_song', methods: ['POST'])]
    public function addPlaylistSong(Request $request, string $playlistId, string $songId): JsonResponse
    {
        $user = Auth::assert($request);
        $playlist = PlaylistAccess::assertEditor($playlistId, $user);

        $stmt = DatabaseConnection::prepare('INSERT INTO playlist_song
            (playlist_id, song_id)
            VALUES
            (:playlistId, :songId);'
        );
        $stmt->bindParam(':playlistId', $playlistId, QueryParam::STR);
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }

    /**
     * Route 8
     */
    #[Route('/{playlistId<\d+>}/songs/{songId<\d+>}', name: 'remove_song', methods: ['DELETE'])]
    public function removePlaylistSong(Request $request, string $playlistId, string $songId): JsonResponse
    {
        $user = Auth::assert($request);
        $playlist = PlaylistAccess::assertEditor($playlistId, $user);

        $stmt = DatabaseConnection::prepare('DELETE FROM playlist_song
            WHERE
                playlist_id = :playlistId
                AND song_id = :songId;'
        );
        $stmt->bindParam(':playlistId', $playlistId, QueryParam::STR);
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }
}
