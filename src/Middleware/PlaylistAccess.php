<?php
namespace App\Middleware;

use App\Utils\DatabaseConnection;
use App\Utils\DatabaseQueryOutputFixer;
use App\Utils\QueryFetch;
use App\Utils\QueryParam;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PlaylistAccess
{
    /**
     * Will throw an exception if the user has no right to view
     * the specified playlist.
     * 
     * @param string      $playlistId  The ID of the playlist to consider.
     * @param null|array  $user        The user array returned by functions of middleware Auth,
     *                                 or `null` if the user is not authenticated.
     * @param null|string $message     The message sent to user in case the user
     *                                 is forbidden to view the playlist.
     * 
     * @return array an associative array containing all fields of the row
     * in table `playlists` that corresponds to the specified playlist ID.
     */
    public static function assertViewer(string $playlistId, ?array $user = null, ?string $message = null): array
    {
        $playlist = self::readPlaylist($playlistId);
        if (!$playlist['is_public']) {
            if ($playlist['owned_by'] != $user['id']) {
                throw new UnauthorizedHttpException('None', $message ?? 'You don\'t have the right to view this playlist.');
            }
        }
        return $playlist;
    }

    /**
     * Will throw an exception if the user has no right to edit
     * the specified playlist.
     * 
     * @param string      $playlistId  The ID of the playlist to consider.
     * @param array       $user        The user array returned by functions of middleware Auth.
     *                                 Unlike `assertViewer`, this parameter is mandatory.
     * @param null|string $message     The message sent to user in case the user
     *                                 is forbidden to edit the playlist.
     * 
     * @return array an associative array containing all fields of the row
     * in table `playlists` that corresponds to the specified playlist ID.
     */
    public static function assertEditor(string $playlistId, array $user, ?string $message = null): array
    {
        $playlist = self::readPlaylist($playlistId);
        if ($playlist['owned_by'] != $user['id']) {
            throw new UnauthorizedHttpException('None', $message ?? 'You don\'t have the right to edit this playlist.');
        }
        return $playlist;
    }

    private static function readPlaylist(string $playlistId): array
    {
        $stmt = DatabaseConnection::prepare('SELECT
            id, name, owned_by, is_public,
            added_at, updated_at
            FROM playlists
            WHERE id = :playlistId;'
        );
        $stmt->bindParam(':playlistId', $playlistId, QueryParam::STR);
        $stmt->execute();

        $playlist = $stmt->fetch(QueryFetch::ASSOC);
        if (!$playlist) {
            throw new BadRequestHttpException('Invalid playlist ID.');
        }

        DatabaseQueryOutputFixer::fixBool($playlist, 'is_public');
        DatabaseQueryOutputFixer::fixDateTime($playlist, 'added_at');
        DatabaseQueryOutputFixer::fixDateTime($playlist, 'updated_at');
        return $playlist;
    }
}
