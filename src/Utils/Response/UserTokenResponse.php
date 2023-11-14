<?php
namespace App\Utils\Response;

use App\Utils\Authenticator;

class UserTokenResponse extends NormalizedJsonResponse
{
    public function __construct(array $user)
    {
        $user['token'] = Authenticator::issueToken($user['id']);
        unset($user['password_hash']);
        parent::__construct($user, 200);
    }
}
