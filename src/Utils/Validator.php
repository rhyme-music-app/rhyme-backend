<?php
namespace App\Utils;

use App\Utils\Exception\ValidationException;

class Validator
{
    private static $specs_ArtistUpdate = [
        'name' => 'artists.name',
        'type' => 'artists.type'
    ];

    public static function validateArtistUpdate_AllMustPresent(array $array, ?string $message = null): void
    {
        /*return*/ self::validateArray_AllMustPresent($array, self::$specs_ArtistUpdate, $message);
    }

    public static function validateArtistUpdate_AllAreOptional(array $array, ?string $message = null): array {
        return self::validateArray_AllAreOptional($array, self::$specs_ArtistUpdate, $message);
    }

    private static $specs_GenreUpdate = [
        'name' => 'genres.name',
    ];
    
    public static function validateGenreUpdate_AllMustPresent(array $array, ?string $message = null): void
    {
        /*return*/ self::validateArray_AllMustPresent($array, self::$specs_GenreUpdate, $message);
    }

    public static function validateGenreUpdate_AllAreOptional(array $array, ?string $message = null): array
    {
        return self::validateArray_AllAreOptional($array, self::$specs_GenreUpdate, $message);
    }

    private static $specs_UserUpdate = [
        'email' => 'users.email',
        'password' => 'users.password',
        'name' => 'users.name',
    ];

    public static function validateUserUpdate_AllMustPresent(array $array, ?string $message = null): void
    {
        /*return*/ self::validateArray_AllMustPresent($array, self::$specs_UserUpdate, $message);
    }

    public static function validateUserUpdate_AllAreOptional(array $array, ?string $message = null): array
    {
        return self::validateArray_AllAreOptional($array, self::$specs_UserUpdate, $message);
    }

    public static function validateArray_AllMustPresent(array $array, array $specs, ?string $message = null): void
    {
        foreach ($specs as $key => $path) {
            if (!array_key_exists($key, $array)) {
                throw new ValidationException($key, 'is missing', $message);
            }
            self::validate($array[$key], $path, $key, $message);
        }
    }

    /**
     * @return \array an indexed array containing all present fields.
     */
    public static function validateArray_AllAreOptional(array $array, array $specs, ?string $message = null): array
    {
        $availableFields = [];
        foreach ($specs as $key => $path) {
            if (array_key_exists($key, $array)) {
                self::validate($array[$key], $path, $key, $message);
                $availableFields[] = $key;
            }
        }
        return $availableFields;
    }

    public static function validate(mixed &$value, string $path, string $keyName, ?string $message): void
    {
        switch ($path) {
            case 'users.email':
                self::assertAsciiAndNotEmpty($value, $path, $keyName, $message);
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new ValidationException($keyName, 'is invalid', $message);
                }
                break;

            case 'users.password':
                self::assertAsciiAndNotEmpty($value, $path, $keyName, $message);
                $N = strlen($value);
                if ($N < 8) {
                    throw new ValidationException($keyName, 'is too short (minimum 8 characters, maximum 24 characters)', $message);
                }
                else if ($N > 24) {
                    throw new ValidationException($keyName, 'is too long (minimum 8 characters, maximum 24 characters)', $message);
                }
                break;
            
            case 'users.name':
            case 'genres.name':
            case 'artists.name':
            case 'artists.type': // TODO: artists.type is an enum, not an arbitrary string !
                self::assertAsciiAndNotEmpty($value, $path, $keyName, $message);
                break;
        }
    }

    private static function assertAsciiAndNotEmpty(mixed &$value, string $path, string $keyName, ?string $message): void
    {
        if (!$value) {
            throw new ValidationException($keyName, 'is empty', $message);
        }
        // https://stackoverflow.com/a/6497946/13680015
        if (preg_match('/[^\x20-\x7e]/', $value)) {
            throw new ValidationException($keyName, 'contains invalid characters', $message);
        }
    }
}
