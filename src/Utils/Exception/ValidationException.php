<?php
namespace App\Utils\Exception;

use IntlChar;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidationException extends BadRequestHttpException
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
