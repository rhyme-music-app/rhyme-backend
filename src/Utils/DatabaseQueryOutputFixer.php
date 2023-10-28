<?php
namespace App\Utils;

class DatabaseQueryOutputFixer
{
    /**
     * Fixes boolean fields of query result.
     * 
     * When fetching associative array from MySQL,
     * boolean values are converted to integers
     * 0 and 1 instead of false and true.
     * 
     * This is because MySQL considers BOOLEAN
     * as TINYINT data type.
     * 
     * This function converts those TINYINT constants
     * back to their true/false counterparts.
     */
    public static function fixBool(array &$a, string $fieldName): void
    {
        $a[$fieldName] = ($a[$fieldName] != 0 ? true : false);
    }

    /**
     * Fixes datetime fields of query result.
     * 
     * When fetching associative array from MySQL,
     * datetime values are represented by strings
     * of this format:
     *    Year-Month-Day Hour:Minute:Second
     * 
     * (24-hour ; all leading zeros e.g. month 04,
     * hour 09, second 01).
     * 
     * This function converts those datetime strings
     * to PHP DateTime objects.
     */
    public static function fixDateTime(array &$a, string $fieldName): void
    {
        $a[$fieldName] = DateTimeUtils::convertSQLDateTimeToPHPDateTime($a[$fieldName]);
    }
}
