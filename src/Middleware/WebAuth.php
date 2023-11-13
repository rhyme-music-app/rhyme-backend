<?php
namespace App\Middleware;

use App\Controller\API\AuthController;
use App\Utils\Authenticator;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Functionality same as Auth class, but specialized
 * for web controllers' use.
 * 
 * For documentation, just read the documentation of
 * corresponding functions in the Auth class.
 */
class WebAuth
{
    /**
     * This function is not available in Auth class, though.
     * It returns an array that could be later injected into
     * Twig templates as the `user` attribute so that they
     * know whether the user has been authenticated, and act
     * accordingly. See the body of HomeController's functions
     * to better understand the purpose.
     */
    public static function getTwigUserPayload(Request $request): array
    {
        $user = self::getUserNoException($request);
        if (!$user) {
            $user = [
                'authenticated' => false,
            ];
        } else {
            $user['authenticated'] = true;
        }

        return $user;
    }

    public static function getUserNoException(Request $request): array|null
    {
        $user = null;
        try {
            $user = self::assertAdmin($request);
        } catch (HttpException $e) {
            // let $user be null still
        }
        return $user;
    }

    public static function assertAdmin(Request $request, ?string $message = null): array
    {
        $token = self::extractTokenFromRequest($request);
        $user = Authenticator::verifyToken($token);
        if (!$user['is_admin']) {
            throw new AccessDeniedHttpException($message ?? 'You must be an admin to perform this action.');
        }
        return $user;
    }

    public static function assertAndLogout(Request $request): void
    {
        $token = self::extractTokenFromRequest($request);
        Authenticator::verifyAndRevokeToken($token);
    }

    /**
     * Returns a callable that must be later applied to
     * the Response object in order to have the token cookie
     * reside on the client's browser.
     * 
     * @example
     * $fixResponse = WebAuth::login($request);
     * $fixResponse($response);
     * return $response;
     */
    public static function login(Request $request): callable
    {
        $data = [
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ];
        $user = AuthController::_internal_validate_login($data);
        if (!$user['is_admin']) {
            throw new AccessDeniedHttpException($message ?? 'You must be an admin to login to this page.');
        }
        $token = Authenticator::issueToken($user['id']);
        return fn (Response $response) => $response->headers->setCookie(new Cookie('token', $token));
    }

    private static function extractTokenFromRequest(Request $request): string
    {
        $cookies = $request->cookies;

        if (!$cookies->has('token')) {
            throw new UnauthorizedHttpException('Bearer', 'User not logged in.');
        }

        return $cookies->get('token');
    }
}
