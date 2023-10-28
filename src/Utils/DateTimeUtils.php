<?php
namespace App\Utils;

use DateTime;
use DateTimeZone;
use PDO;

class DateTimeUtils
{
    public static function convertPHPDateTimeToSQLDateTime(DateTime $date): string
    {
        $sqlDate = clone $date;
        $sqlDate->setTimezone(new DateTimeZone(self::getSQLTimeZone()));
        return $sqlDate->format('Y-m-d H:i:s');
    }

    public static function convertSQLDateTimeToPHPDateTime(string $date): DateTime
    {
        $phpDate = DateTime::createFromFormat('Y-m-d H:i:s', $date, new DateTimeZone(self::getSQLTimeZone()));
        // PHP date default time zone is set in src/EventSubscriber/BootSubscriber.php
        $phpDate->setTimezone(new DateTimeZone(date_default_timezone_get()));
        return $phpDate;
    }

    /**
     * Converts given PHP DateTime object to its JS datestring
     * representation, which could be directly used in frontend
     * JavaScript code.
     * 
     * For example, the PHP Datetime:
     *    2023-01-01, 15:15:15, Asia/Ho_Chi_Minh
     * will be converted to string:
     *    2023-01-01T15:15:15+07:00
     * which could be used in JavaScript:
     *    d = new Date('2023-01-01T15:15:15+07:00');
     *    d.getFullYear(); // emits 2023
     *    d.getHours();    // emits 15
     */
    public static function convertPHPDateTimeToJSDateString(DateTime $date): string
    {
        return $date->format('Y-m-d') . 'T' . $date->format('H:i:sP');
    }

    private static $sqlTimeZone = null;

    private static function getSQLTimeZone(): string
    {
        if (self::$sqlTimeZone === null) {
            // https://stackoverflow.com/questions/2934258/how-do-i-get-the-current-time-zone-of-mysql/2934271#comment22736707_3984412
            $tzstmt = DatabaseConnection::getPDO()->prepare('SELECT TIMEDIFF(NOW(), UTC_TIMESTAMP) tz;');
            $tzstmt->execute();
            $tz = $tzstmt->fetch(PDO::FETCH_ASSOC)['tz'];
            // If timezone is GMT-7, MySQL will return -07:00:00 and this timezone string is accepted by PHP.
            // If timezone is GMT+7, MySQL will return 07:00:00, which is not accepted by PHP.
            // So we precede a plus sign to the string to transform it to +07:00:00, which is accepted by PHP.
            if ($tz[0] != '-') {
                $tz = '+' . $tz;
            }
            self::$sqlTimeZone = $tz;
        }
        return self::$sqlTimeZone;
    }
}
