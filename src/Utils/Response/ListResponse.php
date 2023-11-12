<?php
namespace App\Utils\Response;

use App\Utils\DatabaseConnection;
use App\Utils\DatabaseStatement;

class ListResponse extends NormalizedJsonResponse
{
    public static function selectAllFromOneTable(string $table, array $fieldsToSelect): ListResponse
    {
        $fieldList = implode(', ', $fieldsToSelect);
        $stmt = DatabaseConnection::prepare("SELECT $fieldList FROM $table WHERE 1;");
        return new ListResponse($stmt, $fieldsToSelect);
    }

    public static function withCustomQuery(DatabaseStatement $stmt, array $fieldsToSelect): ListResponse
    {
        return new ListResponse($stmt, $fieldsToSelect);
    }

    private function __construct(DatabaseStatement $stmt, array $fieldsToSelect)
    {
        $stmt->execute();

        $list = array_map(
            fn ($row) => self::extractRow($row, $fieldsToSelect),
            $stmt->fetchAll()
        );

        parent::__construct([
            'list' => $list
        ], 200);
    }

    private static function extractRow(array $row, array &$fieldsToSelect): array
    {
        $result = [];
        foreach ($fieldsToSelect as $field) {
            $result[$field] = $row[$field];
        }
        return $result;
    }
}
