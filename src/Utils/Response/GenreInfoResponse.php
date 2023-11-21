<?php
namespace App\Utils\Response;

use App\Utils\DatabaseConnection;
use App\Utils\QueryParam;
use App\Utils\QueryFetch;
use App\Utils\DatabaseQueryOutputFixer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GenreInfoResponse extends NormalizedJsonResponse
{
    public function __construct(string $genreId)
    {
        $stmt = DatabaseConnection::prepare(
            'SELECT
                id, name, image_link, added_at, updated_at,
                added_by, updated_by
            FROM genres
            WHERE id = :genreId;'
        );
        $stmt->bindParam(':genreId', $genreId, QueryParam::STR);
        $stmt->execute();

        $json = $stmt->fetch(QueryFetch::ASSOC);
        if (!$json) {
            throw new BadRequestHttpException('Invalid genre ID.');
        }

        DatabaseQueryOutputFixer::fixDateTime($json, 'added_at');
        DatabaseQueryOutputFixer::fixDateTime($json, 'updated_at');

        parent::__construct($json, 200);
    }
}
