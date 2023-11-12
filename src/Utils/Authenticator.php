<?php
namespace App\Utils;

use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use UnexpectedValueException;

class Authenticator
{
    public static function issueToken(string $userId): string
    {
        $now = new DateTime();

        $stmt = DatabaseConnection::prepare('INSERT INTO tokens (user_id, issued_at) VALUES (:user_id, :issued_at)');
        $stmt->bindParam(':user_id', $userId, QueryParam::STR);
        $stmt->bindParam(':issued_at', $now, QueryParam::DATETIME);
        $stmt->execute();
        $tokenId = DatabaseConnection::lastInsertId();

        $payload = [
            'iss' => 'Rhyme API backend',
            'type' => 'user_authentication',
            'tokenId' => $tokenId,
        ];

        return JWT::encode($payload, $_ENV['APP_SECRET'], 'HS256');
    }

    /**
     * Will throw an exception if the token is malformed.
     * 
     * @return array an associative array containing all fields of the row
     * in table `users` that corresponds to the authenticated user.
     */
    public static function verifyToken(string $token): array
    {
        return self::verifyTokenInternally($token)['user'];
    }

    /**
     * Will throw an exception if the token is malformed.
     */
    public static function verifyAndRevokeToken(string $token): void
    {
        $tokenId = self::verifyTokenInternally($token)['tokenId'];
        $stmt = DatabaseConnection::prepare('DELETE FROM tokens WHERE id = :tokenId;');
        $stmt->bindParam(':tokenId', $tokenId, QueryParam::STR);
        $stmt->execute();
    }

    /**
     * Will throw an exception if the token is malformed.
     * 
     * @return array An array of the form [
     *     'tokenId' => 'the token ID in database',
     *     'user' => {an associative array containing all fields of the row in table `users` that corresponds to the authenticated user.}
     * ]
     */
    private static function verifyTokenInternally(string $token): array
    {
        $E = new BadRequestHttpException('Malformed token');
        $payload = null;

        try {
            $payload = (array) JWT::decode($token, new Key($_ENV['APP_SECRET'], 'HS256'));
        } catch (UnexpectedValueException $e) {
            throw $E;
        }

        foreach (['iss', 'type', 'tokenId'] as $k) {
            if (!array_key_exists($k, $payload)) {
                throw $E; 
            }
        }

        if (
            ($payload['iss'] !== 'Rhyme API backend')
            || ($payload['type'] !== 'user_authentication')
        ) {
            throw $E;
        }

        $tokenId = $payload['tokenId'];
        $stmt = DatabaseConnection::prepare('SELECT user_id FROM tokens WHERE id = :tokenId');
        $stmt->bindParam(':tokenId', $tokenId, QueryParam::STR);
        $stmt->execute();

        $t = $stmt->fetch(QueryFetch::ASSOC);
        if ($t === false) {
            throw $E;
        }

        $userId = $t['user_id'];
        $stmt = DatabaseConnection::prepare('SELECT * FROM users WHERE id = :userId');
        $stmt->bindParam(':userId', $userId, QueryParam::STR);
        $stmt->execute();
        $user = $stmt->fetch(QueryFetch::ASSOC);
        if (false === $user) {
            // This situation should never happen, since the `tokens` table has
            // foreign key constraint to field `id` of table `users`, with
            // ON UPDATE CASCADE and ON DELETE CASCADE.
            // This odd case will only take place if database seeding malfunctions
            // (e.g. populating the database with mock data that is written by
            // humans). 
            throw $E;
        }

        return [
            'tokenId' => $tokenId,
            'user' => $user,
        ];
    }
}
