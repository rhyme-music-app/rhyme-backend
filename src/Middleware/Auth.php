<?php
namespace App\Middleware;

use App\Utils\Authenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Auth
{
    /**
     * Will throw an exception if the user is not authenticated.
     * 
     * @return array an associative array containing all fields of the row
     * in table `users` that corresponds to the authenticated user.
     */
    public static function assert(Request $request): array
    {
        $token = self::extractTokenFromRequest($request);
        return Authenticator::verifyToken($token);
    }

    public static function assertAndLogout(Request $request): void
    {
        $token = self::extractTokenFromRequest($request);
        Authenticator::verifyAndRevokeToken($token);
    }

    private static function extractTokenFromRequest(Request $request): string
    {
        $authHeader = $request->headers->get('Authorization');
        // https://stackoverflow.com/a/1252710/13680015
        $pos = -1;
        if (!$authHeader || ($pos = strpos($authHeader, 'Bearer ')) !== 0)
        {
            throw new UnauthorizedHttpException('Bearer', 'User not logged in.');
        }
        $token = substr_replace($authHeader, '', $pos, strlen('Bearer '));
        return $token;
    }
}
