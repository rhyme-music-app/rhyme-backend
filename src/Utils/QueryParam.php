<?php
namespace App\Utils;

use PDO;

class QueryParam
{
    public const STR = PDO::PARAM_STR;

    public const BOOL = PDO::PARAM_BOOL;

    public const INT = PDO::PARAM_INT;

    public const NULL = PDO::PARAM_NULL;
}
