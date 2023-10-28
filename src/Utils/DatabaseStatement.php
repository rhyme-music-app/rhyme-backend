<?php
namespace App\Utils;

use PDO;
use PDOStatement;

class DatabaseStatement
{
    private PDOStatement $stmt;

    private array $customParams;

    public function __construct(PDOStatement $stmt)
    {
        $this->customParams = [];
        $this->stmt = $stmt;
    }

    /**
     * @param param - The placeholder, e.g. :id, :email.
     * @param value - The value to place in, e.g. 12, "email@example.com".
     * @param type - A value of enum App\Utils\QueryParam.
     */
    public function bindParam(int|string $param, mixed &$value, int $type): void
    {
        if ($type === QueryParam::DATETIME) {
            // https://stackoverflow.com/a/22432400/13680015
            $this->customParams[$param] = [
                'param' => $param,
                'reference' => &$value,
                'type' => $type,
            ];
        } else {
            $this->stmt->bindParam($param, $value, $type);
        }
    }

    public function execute()
    {
        foreach ($this->customParams as $cp) {
            if ($cp['type'] == QueryParam::DATETIME) {
                $sqlDateTime = DateTimeUtils::convertPHPDateTimeToSQLDateTime($cp['reference']);
                $this->stmt->bindValue($cp['param'], $sqlDateTime, PDO::PARAM_STR);
            }
        }
        return $this->stmt->execute();
    }

    /**
     * Same as PDOStatement::fetch().
     */
    public function fetch()
    {
        return $this->stmt->fetch(...func_get_args());
    }

    /**
     * Same as PDOStatement::fetchAll().
     */
    public function fetchAll()
    {
        return $this->stmt->fetch(...func_get_args());
    }
}
