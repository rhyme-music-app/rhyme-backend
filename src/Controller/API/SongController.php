<?php

namespace App\Controller\API;

use App\Middleware\Auth;
use App\Utils\DatabaseConnection;
use App\Utils\QueryParam;
use App\Utils\Response\ListResponse;
use App\Utils\Response\NormalizedJsonResponse;
use App\Utils\Response\SongInfoResponse;
use App\Utils\Validator;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('songs', name: 'songs_')]
class SongController extends AbstractController
{
    /**
     * Route 1
     */
    #[Route(['', '/'], name: 'index', methods: ['GET'])]
    public function indexSongs(): JsonResponse
    {
        return ListResponse::selectAllFromOneTable('songs', [
            'id', 'name', 'audio_link',
            'added_at', 'updated_at',
            'added_by', 'updated_by',
            'streams'
        ]);
    }

    /**
     * Route 2
     */
    #[Route('/{songId<\d+>}', name: 'get', methods: ['GET'])]
    public function getSong(string $songId): JsonResponse
    {
        return new SongInfoResponse($songId);
    }

    /**
     * Route 3
     */
    #[Route(['', '/'], name: 'add', methods: ['POST'])]
    public function addSong(Request $request): JsonResponse
    {
        $user = Auth::assertAdmin($request, 'You cannot add a new song if you are not an admin.');
        $userId = $user['id'];
        $data = json_decode($request->getContent(), true);
        Validator::validateSongUpdate_AllMustPresent($data);

        $stmt = DatabaseConnection::prepare('INSERT INTO songs
        (name, audio_link, added_at, updated_at, added_by, updated_by, streams)
        VALUES
        (:name, :audio_link, :added_at, :updated_at, :added_by, :updated_by, 0);');

        $now = new DateTime();
        $stmt->bindParam(':name', $data['name'], QueryParam::STR);
        $stmt->bindParam(':audio_link', $data['audio_link'], QueryParam::STR);
        $stmt->bindParam(':added_at', $now, QueryParam::DATETIME);
        $stmt->bindParam(':updated_at', $now, QueryParam::DATETIME);
        $stmt->bindParam(':added_by', $userId, QueryParam::STR);
        $stmt->bindParam(':updated_by', $userId, QueryParam::STR);
        $stmt->execute();

        return new SongInfoResponse(DatabaseConnection::lastInsertId());
    }

    /**
     * Route 4
     */
    #[Route('/{songId<\d+>}', name: 'update', methods: ['PUT'])]
    public function updateSong(Request $request, string $songId): JsonResponse
    {
        $user = Auth::assertAdmin($request, "You cannot update a song if you are not an admin.");
        $data = json_decode($request->getContent(), true);

        $availableFields = Validator::validateSongUpdate_AllAreOptional($data);

        if (!empty($availableFields)) {
            $updates = [];
            foreach ($availableFields as $field) {
                $updates[$field] = [
                    'value' => $data[$field],
                    'type' => QueryParam::STR,
                ];
            }
            $now = new DateTime();
            $updates['updated_at'] = [
                'value' => $now,
                'type' => QueryParam::DATETIME,
            ];
            $updates['updated_by'] = [
                'value' => $user['id'],
                'type' => QueryParam::STR,
            ];
            $stmt = DatabaseConnection::prepareUpdateAndBind('songs', $songId, $updates);
            $stmt->execute();
        }
        
        return new SongInfoResponse($songId);
    }

    /**
     * Route 5
     */
    #[Route('/{songId<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function deleteSong(Request $request, string $songId): JsonResponse
    {
        $user = Auth::assertAdmin($request, 'You cannot delete a song if you are not an admin.');

        $stmt = DatabaseConnection::prepare('DELETE FROM songs WHERE id = :songId;');
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }

    /**
     * Route 6
     */
    #[Route('/{songId<\d+>}/listen', name: 'listen', methods: ['GET'])]
    public function listenToSong(string $songId): JsonResponse
    {
        // Retrieve the song's information via SongInfoResponse first
        // to have that class verify the song ID's validity.
        $info = new SongInfoResponse($songId);

        // Then, we increment stream count.
        $stmt = DatabaseConnection::prepare('UPDATE songs
            SET streams = streams + 1
            WHERE id = :songId;'
        );
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        $stmt->execute();

        return $info;
    }

    /**
     * Route 7
     */
    #[Route('/{songId<\d+>}/artists', name: 'get_artists', methods: ['GET'])]
    public function getSongArtists(string $songId): JsonResponse
    {
        $artistFields = [
            'id', 'name', 'type',
            'added_at', 'updated_at',
            'added_by', 'updated_by'
        ];
        $stmt = DatabaseConnection::prepare('SELECT
            ' . implode(', ', $artistFields) . '
            FROM
                artists a
            INNER JOIN artist_song pivot
                ON pivot.artist_id = a.id
            WHERE
                pivot.song_id = :songId;'
        );
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        return ListResponse::withCustomQuery($stmt, $artistFields);
    }

    /**
     * Route 8
     */
    #[Route('/{songId<\d+>}/artists/{artistId<\d+>}', name: 'add_artist', methods: ['POST'])]
    public function addSongArtist(Request $request, string $songId, string $artistId): JsonResponse
    {
        $user = Auth::assertAdmin($request, 'You cannot update a song if you are not an admin.');

        $stmt = DatabaseConnection::prepare('INSERT INTO artist_song
            (artist_id, song_id)
            VALUES
            (:artistId, :songId);'
        );
        $stmt->bindParam(':artistId', $artistId, QueryParam::STR);
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }

    /**
     * Route 9
     */
    #[Route('/{songId<\d+>}/artists/{artistId<\d+>}', name: 'remove_artist', methods: ['DELETE'])]
    public function removeSongArtist(Request $request, string $songId, string $artistId): JsonResponse
    {
        $user = Auth::assertAdmin($request, 'You cannot update a song if you are not an admin.');

        $stmt = DatabaseConnection::prepare('DELETE FROM artist_song
            WHERE
                artist_id = :artistId
                AND song_id = :songId;'
        );
        $stmt->bindParam(':artistId', $artistId, QueryParam::STR);
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }

    /**
     * Route 10
     */
    #[Route('/{songId<\d+>}/genres', name: 'get_genres', methods: ['GET'])]
    public function getSongGenres(string $songId): JsonResponse
    {
        $genreFields = [
            'id', 'name',
            'added_at', 'updated_at',
            'added_by', 'updated_by'
        ];
        $stmt = DatabaseConnection::prepare('SELECT
            ' . implode(', ', $genreFields) . '
            FROM
                genres g
            INNER JOIN genre_song pivot
                ON pivot.genre_id = g.id
            WHERE
                pivot.song_id = :songId;'
        );
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        return ListResponse::withCustomQuery($stmt, $genreFields);
    }

    /**
     * Route 11
     */
    #[Route('/{songId<\d+>}/genres/{genreId<\d+>}', name: 'add_genre', methods: ['POST'])]
    public function addSongGenre(Request $request, string $songId, string $genreId): JsonResponse
    {
        $user = Auth::assertAdmin($request, 'You cannot update a song if you are not an admin.');

        $stmt = DatabaseConnection::prepare('INSERT INTO genre_song
            (genre_id, song_id)
            VALUES
            (:genreId, :songId);'
        );
        $stmt->bindParam(':genreId', $genreId, QueryParam::STR);
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }

    /**
     * Route 12
     */
    #[Route('/{songId<\d+>}/genres/{genreId<\d+>}', name: 'remove_genre', methods: ['DELETE'])]
    public function removeSongGenre(Request $request, string $songId, string $genreId): JsonResponse
    {
        $user = Auth::assertAdmin($request, 'You cannot update a song if you are not an admin.');

        $stmt = DatabaseConnection::prepare('DELETE FROM genre_song
            WHERE
                genre_id = :genreId
                AND song_id = :songId;'
        );
        $stmt->bindParam(':genreId', $genreId, QueryParam::STR);
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }
}
