<?php
namespace App\Utils;

use App\Utils\DatabaseConnection;
use RuntimeException;

class DatabaseResetter
{
    public static function resetDatabase(): void
    {
        $sql = file_get_contents(__DIR__ . '/reset_database.sql');
        if (!$sql) {
            throw new RuntimeException("Cannot open reset_database.sql");
        }
        DatabaseConnection::execute($sql);
    }
}
