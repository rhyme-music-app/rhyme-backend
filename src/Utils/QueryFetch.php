<?php
namespace App\Utils;

use PDO;

class QueryFetch
{
    // https://phpdelusions.net/pdo#fetch
    
    public const ASSOC = PDO::FETCH_ASSOC;

    public const NUM = PDO::FETCH_NUM;

    public const BOTH = PDO::FETCH_BOTH;

    public const OBJ = PDO::FETCH_OBJ;
}
