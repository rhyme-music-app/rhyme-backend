<?php
namespace App\Utils\Response;

use App\Utils\Authenticator;

class UserTokenResponse extends NormalizedJsonResponse
{
    public function __construct(string $userId)
    {
        parent::__construct([
            'token' => Authenticator::issueToken($userId)
        ], 200);
    }
}
