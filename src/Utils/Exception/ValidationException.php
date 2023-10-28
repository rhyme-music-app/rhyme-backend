<?php
namespace App\Utils\Exception;

use Exception;
use IntlChar;

class ValidationException extends Exception
{
    public function __construct(string $keyName, string $fault, ?string $message)
    {
        if (!$message) {
            // Remove underscores from snake_case.
            $keyName = implode(' ', explode('_', $keyName));
            // Make $keyName title-cased.
            if (IntlChar::isalpha($keyName[0])) {
                $keyName[0] = IntlChar::toupper($keyName[0]);
            }
            $message = "$keyName $fault.";
        }
        parent::__construct($message);
    }
}
