<?php
namespace App\Utils;

use PDO;

class DatabaseConnection
{
    public static function execute(string $sql)
    {
        self::prepareConnection();
        return self::$conn->exec($sql);
    }

    public static function prepare(string $sqlWithParams): DatabaseStatement
    {
        self::prepareConnection();
        return new DatabaseStatement(self::$conn->prepare($sqlWithParams));
    }

    public static function lastInsertId(): string|null
    {
        self::prepareConnection();
        return self::$conn->lastInsertId();
    }

    public static function getPDO(): PDO
    {
        self::prepareConnection();
        return self::$conn;
    }

    ///////////////////////////////////////////////////////////

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
