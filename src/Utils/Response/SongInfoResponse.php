<?php
namespace App\Utils\Response;

use App\Utils\DatabaseConnection;
use App\Utils\DatabaseQueryOutputFixer;
use App\Utils\QueryFetch;
use App\Utils\QueryParam;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SongInfoResponse extends NormalizedJsonResponse
{
    public function __construct(string $songId)
    {
        $stmt = DatabaseConnection::prepare(
            'SELECT
                id, name, audio_link, image_link,
                added_at, updated_at,
                added_by, updated_by,
                streams
            FROM songs
            WHERE id = :songId;'
        );
        $stmt->bindParam(':songId', $songId, QueryParam::STR);
        $stmt->execute();

        $json = $stmt->fetch(QueryFetch::ASSOC);
        if (!$json) {
            throw new BadRequestHttpException('Invalid song ID.');
        }

        DatabaseQueryOutputFixer::fixDateTime($json, 'added_at');
        DatabaseQueryOutputFixer::fixDateTime($json, 'updated_at');

        parent::__construct($json, 200);
    }
}