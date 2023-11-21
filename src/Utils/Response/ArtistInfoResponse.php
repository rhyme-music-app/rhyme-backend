<?php
namespace App\Utils\Response;

use App\Utils\DatabaseConnection;
use App\Utils\DatabaseQueryOutputFixer;
use App\Utils\QueryFetch;
use App\Utils\QueryParam;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ArtistInfoResponse extends NormalizedJsonResponse
{
    public function __construct(string $artistId)
    {
        $stmt = DatabaseConnection::prepare(
            'SELECT
                id, name, image_link, added_at, updated_at, added_by, updated_by
            FROM artists
            WHERE id = :artistId;'
        );
        $stmt->bindParam(':artistId', $artistId, QueryParam::STR);
        $stmt->execute();

        $json = $stmt->fetch(QueryFetch::ASSOC);
        if (!$json) {
            throw new BadRequestHttpException('Invalid artist ID.');
        }

        DatabaseQueryOutputFixer::fixDateTime($json, 'added_at');
        DatabaseQueryOutputFixer::fixDateTime($json, 'updated_at');

        parent::__construct($json, 200);
    }
}
