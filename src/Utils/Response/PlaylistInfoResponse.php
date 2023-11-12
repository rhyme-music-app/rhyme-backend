<?php
namespace App\Utils\Response;

use App\Middleware\PlaylistAccess;

class PlaylistInfoResponse extends NormalizedJsonResponse
{
    public function __construct(string $playlistId, array $user)
    {
        $json = PlaylistAccess::assertViewer($playlistId, $user);
        parent::__construct($json, 200);
    }
}
