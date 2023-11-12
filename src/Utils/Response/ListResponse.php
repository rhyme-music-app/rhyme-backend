<?php
namespace App\Utils\Response;

use App\Utils\DatabaseConnection;

class ListResponse extends NormalizedJsonResponse
{
    public function __construct(string $table, array $fieldsToSelect)
    {
        $fieldList = implode(', ', $fieldsToSelect);
        $stmt = DatabaseConnection::prepare("SELECT $fieldList FROM $table WHERE 1;");
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
