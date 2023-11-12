<?php

namespace App\Controller\API;

use App\Middleware\Auth;
use App\Utils\DatabaseConnection;
use App\Utils\QueryParam;
use App\Utils\Response\GenreInfoResponse;
use App\Utils\Response\NormalizedJsonResponse;
use App\Utils\Validator;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/genres', name: 'genres_')]
class GenreController extends AbstractController
{
    #[Route('/{genreId<\d+>}', name: 'index', methods: ['GET'])]
    public function index(string $genreId): JsonResponse
    {
        return new GenreInfoResponse($genreId);
    }

    #[Route(['', '/'], name: 'add', methods: ['POST'])]
    public function addGenre(Request $request): JsonResponse
    {
        $user = Auth::assertAdmin($request, 'You cannot create a new genre if you are not an admin.');
        $userId = $user['id'];
        $data = json_decode($request->getContent(), true);
        Validator::validateGenreUpdate_AllMustPresent($data);

        $stmt = DatabaseConnection::prepare('INSERT INTO genres
        (name, added_at, updated_at, added_by, updated_by)
        VALUES
        (:name, :added_at, :updated_at, :added_by, :updated_by);');

        $now = new DateTime();
        $stmt->bindParam(':name', $data['name'], QueryParam::STR);
        $stmt->bindParam(':added_at', $now, QueryParam::DATETIME);
        $stmt->bindParam(':updated_at', $now, QueryParam::DATETIME);
        $stmt->bindParam(':added_by', $userId, QueryParam::STR);
        $stmt->bindParam(':updated_by', $userId, QueryParam::STR);
        $stmt->execute();

        return new GenreInfoResponse(DatabaseConnection::lastInsertId());
    }

    #[Route('/{genreId<\d+>}', name: 'update', methods: ['PUT'])]
    public function updateGenre(Request $request, string $genreId): JsonResponse
    {
        $user = Auth::assertAdmin($request, 'You cannot update a genre if you are not an admin.');
        $data = json_decode($request->getContent(), true);

        $availableFields = Validator::validateGenreUpdate_AllAreOptional($data);

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
            $stmt = DatabaseConnection::prepareUpdateAndBind('genres', $genreId, $updates);
            $stmt->execute();
        }

        return new GenreInfoResponse($genreId);
    }

    #[Route('/{genreId<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function deleteGenre(Request $request, string $genreId): JsonResponse
    {
        $user = Auth::assertAdmin($request, 'You cannot delete a genre if you are not an admin');

        $stmt = DatabaseConnection::prepare('DELETE FROM genres WHERE id = :genreId');
        $stmt->bindParam(':genreId', $genreId, QueryParam::STR);
        $stmt->execute();

        return new NormalizedJsonResponse([], 200);
    }
}
