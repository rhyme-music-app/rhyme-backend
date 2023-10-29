<?php
namespace App\Utils\Response;

use App\Utils\DateTimeUtils;
use DateTime;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;

class NormalizedJsonResponse extends JsonResponse
{
    public function __construct(array $data, int $status, array $headers = [])
    {
        self::replaceAllPHPDateTimeWithString($data);

        if (array_key_exists('success', $data)) {
            throw new RuntimeException('Key `success` must not be present in JSON data passed to NormalizedJsonResponse.');
        }
        
        if ($status != 200) {
            $data['success'] = false;
        } else {
            $data['success'] = true;
        }

        parent::__construct($data, $status, $headers);
    }

    /**
     * Replaces all PHP DateTime objects inside given JSON data
     * with their string representations, which could be directly
     * used in frontend JavaScript code.
     * 
     * See src/App/Utils/DateTimeUtils.php.
     */
    private static function replaceAllPHPDateTimeWithString(array &$data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = self::replaceAllPHPDateTimeWithString($value);
            } else if ($value instanceof DateTime) {
                $value = DateTimeUtils::convertPHPDateTimeToJSDateString($value);
            } else {
                continue;
            }
            $data[$key] = $value;
        }
    }
}
