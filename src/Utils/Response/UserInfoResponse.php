<?php
namespace App\Utils\Response;

use App\Utils\DatabaseConnection;
use App\Utils\QueryParam;
use App\Utils\QueryFetch;
use App\Utils\DatabaseQueryOutputFixer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserInfoResponse extends NormalizedJsonResponse
{
    public function __construct(string $userId)
    {
        $stmt = DatabaseConnection::prepare(
            'SELECT id, email, name, is_admin, deleted
            FROM users WHERE id = :id;'
        );
        $stmt->bindParam(':id', $userId, QueryParam::STR);
        $stmt->execute();

        $json = $stmt->fetch(QueryFetch::ASSOC);
        if (!$json) {
            throw new BadRequestHttpException('Invalid user ID.');
        }

        DatabaseQueryOutputFixer::fixBool($json, 'is_admin');
        DatabaseQueryOutputFixer::fixBool($json, 'deleted');

        parent::__construct($json, 200);
    }
}
