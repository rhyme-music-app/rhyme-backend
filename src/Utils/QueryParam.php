<?php
namespace App\Utils;

use PDO;

class QueryParam
{
    public const STR = PDO::PARAM_STR;

    public const BOOL = PDO::PARAM_BOOL;

    public const INT = PDO::PARAM_INT;

    public const NULL = PDO::PARAM_NULL;

    // Be careful: The following values must not be set the
    // same as any of PDO::PARAM_* constants, or any set of
    // them bitwised together.

    public const DATETIME = -1;
}
