<?php
namespace App\Middleware;

use App\Utils\Authenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Auth
{
    /**
     * This function tries to retrieve the
     * currently-authenticated user, and
     * returns his information as an associative
     * array.
     * 
     * Unlike `Auth::assert`, if the user has not
     * logged in yet, this function simply returns
     * `null` instead of raising an exception.
     */
    public static function getUserNoException(Request $request): array|null
    {
        $user = null;
        try {
            $user = Auth::assert($request);
        } catch (HttpException $e) {
            // let $user be null still
        }
        return $user;
    }

    /**
     * Will throw an exception if the user is not authenticated,
     * or he is authenticated but is not an admin.
     * 
     * @param \string $message  The message sent to user in case the user
     *                          is not an admin.
     * 
     * @return array an associative array containing all fields of the row
     * in table `users` that corresponds to the authenticated user.
     */
    public static function assertAdmin(Request $request, ?string $message = null): array
    {
        $token = self::extractTokenFromRequest($request);
        $user = Authenticator::verifyToken($token);
        if (!$user['is_admin']) {
            throw new AccessDeniedHttpException($message ?? 'You must be an admin to perform this action.');
        }
        return $user;
    }

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
