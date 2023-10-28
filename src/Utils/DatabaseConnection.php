<?php
namespace App\Utils;

use PDO;
use PDOStatement;

class DatabaseConnection
{
    public static function execute(string $sql): int|false
    {
        self::prepareConnection();
        return self::$conn->exec($sql);
    }

    public static function prepare(string $sqlWithParams): PDOStatement|false
    {
        self::prepareConnection();
        return self::$conn->prepare($sqlWithParams);
    }

    public static function lastInsertId(): string|null
    {
        return self::$conn->lastInsertId();
    }

    private static ?PDO $conn = null;

    private static function prepareConnection(): void
    {
        if (self::$conn !== null) return;

        $dbname = $_ENV['DATABASE_NAME'];
        $dbhost = $_ENV['DATABASE_HOST'];
        $dbport = $_ENV['DATABASE_PORT'];
        $dbuser = $_ENV['DATABASE_USER'];
        $dbpass = $_ENV['DATABASE_PASS'];

        self::$conn = new PDO("mysql:host=$dbhost;port=$dbport;dbname=$dbname", $dbuser, $dbpass);
        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
