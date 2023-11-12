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

    /**
     * Returns a statement that can be executed directly to update
     * one row in a table.
     * 
     * @param \string $tableName    e.g. users.
     * @param \string $id           The ID of the row to update.
     * @param \array $updates       An array of the form:
     * ```php
     * [
     *     'the_field_name' => [
     *         'value' => 'the_value_of_the_field',
     *         'type' => 'QueryParam::STR or QueryParam::xxx',
     *     ],
     *     // For example:
     *     'name' => [
     *         'value' => 'My New Name',
     *         'type' => QueryParam::STR,
     *     ],
     *     'updatedAt' => [
     *         'value' => new DateTime(),
     *         'type' => QueryParam::DATETIME,
     *     ],
     * ]
     * ```
     */
    public static function prepareUpdateAndBind(string $tableName, string $id, array $updates): DatabaseStatement
    {
        $stmt = self::prepare("UPDATE $tableName SET " . implode(', ',
            array_map(
                fn (string $field) => "$field = :$field",
                array_keys($updates)
            )
        ) . ' WHERE id = :id');
        foreach ($updates as $field => &$spec) {
            $value = &$spec['value'];
            $stmt->bindParam(":$field", $value, $spec['type']);
        }
        $stmt->bindParam(':id', $id, QueryParam::STR);
        return $stmt;
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
