<?php
namespace App\Middleware;

use App\Utils\DatabaseConnection;
use App\Utils\DatabaseQueryOutputFixer;
use App\Utils\QueryFetch;
use App\Utils\QueryParam;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class FavoriteAccess
{
    /**
     * Will throw an exception if the user has no right to view
     * favorite playlists/songs of another user.
     * 
     * @param string      $anotherUserId  The ID of "another user" as said above.
     * @param array       $user           The user array returned by functions of middleware Auth,
     *                                    indicating the user that wants to view favorites of another
     *                                    user, or `null` if the user is not authenticated.
     * @param null|string $message        The message sent to user in case the user
     *                                    is forbidden.
     */
    public static function assertViewer(string $anotherUserId, array $user, ?string $message = null): void
    {
        if ($anotherUserId != $user['id']) {
            throw new UnauthorizedHttpException('None', $message ?? 'You don\'t have the right to view another user\'s favorite playlists/songs.');
        }
    }

    /**
     * Will throw an exception if the user has no right to edit
     * favorite playlists/songs of another user.
     * 
     * @param string      $anotherUserId  The ID of "another user" as said above.
     * @param array       $user           The user array returned by functions of middleware Auth,
     *                                    indicating the user that wants to edit favorites of another
     *                                    user, or `null` if the user is not authenticated.
     * @param null|string $message        The message sent to user in case the user
     *                                    is forbidden.
     */
    public static function assertEditor(string $anotherUserId, array $user, ?string $message = null): void
    {
        if ($anotherUserId != $user['id']) {
            throw new UnauthorizedHttpException('None', $message ?? 'You don\'t have the right to add or remove another user\'s favorite playlists/songs.');
        }
    }
}
