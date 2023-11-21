<?php

namespace App\Controller\API;

use App\Middleware\Auth;
use App\Utils\DatabaseConnection;
use App\Utils\QueryParam;
use App\Utils\Response\ArtistInfoResponse;
use App\Utils\Response\ListResponse;
use App\Utils\Response\NormalizedJsonResponse;
use App\Utils\Validator;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/artists', name: 'artists_')]
class ArtistController extends AbstractController
{
    /**
     * Route 1
     */
    #[Route(['', '/'], name: 'index', methods: ['GET'])]
    public function indexArtists(): JsonResponse
    {
        return ListResponse::selectAllFromOneTable('artists', [
            'id', 'name', 'image_link', 'added_at', 'updated_at', 'added_by', 'updated_by'
        ]);
    }

    /**
     * Route 2
     */
    #[Route('/{artistId<\d+>}', name: 'get', methods: ['GET'])]
    public function getArtist(string $artistId): JsonResponse
    {
        return new ArtistInfoResponse($artistId);
    }

    /**
     * Route 3
     */
    #[Route(['', '/'], name: 'add', methods: ['POST'])]
    public function addArtist(Request $request): JsonResponse
    {
        $user = Auth::assertAdmin($request, 'You cannot add a new artist if you are not an admin.');
        $userId = $user['id'];
        $data = json_decode($request->getContent(), true);
        Validator::validateArtistUpdate_AllMustPresent($data);

        $stmt = DatabaseConnection::prepare('INSERT INTO artists
        (name, image_link, added_at, updated_at, added_by, updated_by)
        VALUES
        (:name, :image_link, :added_at, :updated_at, :added_by, :updated_by);');

        $now = new DateTime();
        $data['image_link'] = $data['image_link'] ?? null;
        $stmt->bindParam(':name', $data['name'], QueryParam::STR);
        $stmt->bindParam(':image_link', $data['image_link'], QueryParam::STR);
        $stmt->bindParam(':added_at', $now, QueryParam::DATETIME);
        $stmt->bindParam(':updated_at', $now, QueryParam::DATETIME);
        $stmt->bindParam(':added_by', $userId, QueryParam::STR);
        $stmt->bindParam(':updated_by', $userId, QueryParam::STR);
        $stmt->execute();

        return new ArtistInfoResponse(DatabaseConnection::lastInsertId());
    }

    /**
     * Route 4
     */
    #[Route('/{artistId<\d+>}', name: 'update', methods: ['PUT'])]
    public function updateArtist(Request $request, string $artistId): JsonResponse
    {
        $user = Auth::assertAdmin($request, 'You cannot update an artist if you are not an admin.');
        $data = json_decode($request->getContent(), true);

        $availableFields = Validator::validateArtistUpdate_AllAreOptional($data);

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
            $stmt = DatabaseConnection::prepareUpdateAndBind('artists', $artistId, $updates);
            $stmt->execute();
        }

        return new ArtistInfoResponse($artistId);
    }

    /**
     * Route 5
     */
    #[Route('/{artistId<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function deleteArtist(Request $request, string $artistId): JsonResponse
    {
        $user = Auth::assertAdmin($request, 'You cannot delete an artist if you are not an admin.');

        $stmt = DatabaseConnection::prepare('DELETE FROM artists WHERE id = :artistId;');
        $stmt->bindParam(':artistId', $artistId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }

    /**
     * Route 6
     */
    #[Route('/{artistId<\d+>}/songs', name: 'get_artist_songs', methods: ['GET'])]
    public function getArtistSongs(string $artistId): JsonResponse
    {
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
            INNER JOIN artist_song pivot
                ON pivot.song_id = s.id
            WHERE
                pivot.artist_id = :artistId;'
        );
        $stmt->bindParam(':artistId', $artistId, QueryParam::STR);
        return ListResponse::withCustomQuery($stmt, $songFields);
    }
}
