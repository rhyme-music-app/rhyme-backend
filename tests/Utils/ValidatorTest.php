<?php declare(strict_types=1);
namespace App\Tests\Utils;

use PHPUnit\Framework\TestCase;
use App\Utils\Exception\ValidationException;
use App\Utils\Validator;

final class ValidatorTest extends TestCase {
    public function test_validateUsersEmail(): void {
        $validator = new Validator();
        $email = "someone@example.com";
        $validatedEmail = $email;
        $validator->validate($validatedEmail, "users.email", "key", null);
        $this->assertSame($validatedEmail, $email);

        $email = "@example.com";
        $validatedEmail = $email;
        try {
            $validator->validate($validatedEmail, "users.email", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedEmail, $email);
        }

        $email = "someone@";
        $validatedEmail = $email;
        try {
            $validator->validate($validatedEmail, "users.email", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedEmail, $email);
        }

        $email = "";
        $validatedEmail = $email;
        try {
            $validator->validate($validatedEmail, "users.email", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedEmail, $email);
        }
    }

    public function test_validateUsersPassword(): void {
        $validator = new Validator();
        $password = "password";
        $validatedPassword = $password;
        $validator->validate($validatedPassword, "users.password", "key", null);
        $this->assertSame($validatedPassword, $password);

        $password = "pass";
        $validatedPassword = $password;
        try {
            $validator->validate($validatedPassword, "users.password", "key", null);
            $this->fail("Expected ValidationException not thrown (password too short error)");
        } catch (ValidationException $e) {
            $this->assertSame($validatedPassword, $password);
        }

        $password = "\x1f";
        $validatedPassword = $password;
        try {
            $validator->validate($validatedPassword, "users.password", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedPassword, $password);
        }

        $password = "";
        $validatedPassword = $password;
        try {
            $validator->validate($validatedPassword, "users.password", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedPassword, $password);
        }
    }

    public function test_validateUsersName(): void {
        $validator = new Validator();
        $name = "a\u{1ea1}";
        $validatedName = $name;
        $validator->validate($validatedName, "users.name", "key", null);
        $this->assertSame($validatedName, $name);

        $name = "\x1f";
        $validatedName = $name;
        try {
            $validator->validate($validatedName, "users.name", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedName, $name);
        }

        $name = "";
        $validatedName = $name;
        try {
            $validator->validate($validatedName, "users.name", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedName, $name);
        }
    }

    public function test_validateArtistsType(): void {
        $validator = new Validator();
        $type = "pianist";
        $validatedType = $type;
        $validator->validate($validatedType, "artists.type", "key", null);
        $this->assertSame($validatedType, $type);

        $type = "\u{1ea1}";
        $validatedType = $type;
        try {
            $validator->validate($validatedType, "artists.type", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedType, $type);
        }

        $type = "";
        $validatedType = $type;
        try {
            $validator->validate($validatedType, "artists.type", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedType, $type);
        }

        $type = null;
        $validatedType = $type;
        try {
            $validator->validate($validatedType, "artists.type", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedType, $type);
        }
    }

    public function test_validateSongsAudioLink(): void {
        $validator = new Validator();
        $audioLink = "https://example.com/audio.mp3";
        $validatedAudioLink = $audioLink;
        $validator->validate($validatedAudioLink, "songs.audio_link", "key", null);
        $this->assertSame($validatedAudioLink, $audioLink);

        $audioLink = "https:://example.com/audio.mp3";
        $validatedAudioLink = $audioLink;
        try {
            $validator->validate($validatedAudioLink, "songs.audio_link", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedAudioLink, $audioLink);
        }

        $audioLink = "\u{1ea1}";
        $validatedAudioLink = $audioLink;
        try {
            $validator->validate($validatedAudioLink, "songs.audio_link", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedAudioLink, $audioLink);
        }

        $audioLink = null;
        $validatedAudioLink = $audioLink;
        try {
            $validator->validate($validatedAudioLink, "songs.audio_link", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedAudioLink, $audioLink);
        }
    }

    public function test_validatePlaylistsIsPublic(): void {
        $validator = new Validator();
        $isPublic = true;
        $validatedIsPublic = $isPublic;
        $validator->validate($validatedIsPublic, "playlists.is_public", "key", null);
        $this->assertSame($validatedIsPublic, $isPublic);

        $isPublic = false;
        $validatedIsPublic = $isPublic;
        $validator->validate($validatedIsPublic, "playlists.is_public", "key", null);
        $this->assertSame($validatedIsPublic, $isPublic);

        $isPublic = "true";
        $validatedIsPublic = $isPublic;
        try {
            $validator->validate($validatedIsPublic, "playlists.is_public", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedIsPublic, $isPublic);
        }

        $isPublic = null;
        $validatedIsPublic = $isPublic;
        try {
            $validator->validate($validatedIsPublic, "playlists.is_public", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedIsPublic, $isPublic);
        }
    }

    public function test_validateArray_AllMustPresent(): void {
        $validator = new Validator();
        $array = [
            'name' => 'a',
            'is_public' => true,
            'image_link' => 'https://example.com/image.png',
        ];
        $validator->validateArray_AllMustPresent($array, [
            'name' => 'playlists.name',
            'is_public' => 'playlists.is_public',
            'image_link' => 'playlists.image_link',
        ], null);
        $this->assertSame($array['name'], 'a');
        $this->assertSame($array['is_public'], true);
        $this->assertSame($array['image_link'], 'https://example.com/image.png');

        $array = [
            'name' => 'a',
            'is_public' => true,
        ];
        try {
            $validator->validateArray_AllMustPresent($array, [
                'name' => 'playlists.name',
                'is_public' => 'playlists.is_public',
                'image_link' => 'playlists.image_link',
            ], null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($array['name'], 'a');
            $this->assertSame($array['is_public'], true);
        }
    }

    public function test_validateArray_AllAreOptional(): void {
        $validator = new Validator();
        $array = [
            'name' => 'a',
            'is_public' => true,
            'image_link' => 'https://example.com/image.png',
        ];
        $availableFields = $validator->validateArray_AllAreOptional($array, [
            'name' => 'playlists.name',
            'is_public' => 'playlists.is_public',
            'image_link' => 'playlists.image_link',
        ], null);
        foreach ($availableFields as $field) {
            $this->assertArrayHasKey($field, $array);
        }
        $this->assertArrayHasKey('image_link', $array);

        $array = [
            'name' => 'a',
            'is_public' => true,
        ];
        $availableFields = $validator->validateArray_AllAreOptional($array, [
            'name' => 'playlists.name',
            'is_public' => 'playlists.is_public',
            'image_link' => 'playlists.image_link',
        ], null);
        foreach ($availableFields as $field) {
            $this->assertArrayHasKey($field, $array);
        }
        $this->assertArrayNotHasKey('image_link', $array);
    }

    public function test_assertUnicodeNoSpecialCharsAndNotEmpty(): void {
        $validator = new Validator();
        $str = "\u{0058}\u{0069}\u{006e}\u{0020}\u{0063}\u{0068}\u{00e0}\u{006f}\u{0020}\u{0063}\u{00e1}\u{0063}\u{0020}\u{0062}\u{1ea1}\u{006e}";
        $validatedStr = $str;
        $validator->_assertUnicodeNoSpecialCharsAndNotEmpty($validatedStr, "path", "key", null);
        $this->assertSame($validatedStr, $str);

        $str = "a\x1f";
        $validatedStr = $str;
        try {
            $validator->_assertUnicodeNoSpecialCharsAndNotEmpty($validatedStr, "path", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedStr, $str);
        }

        $str = "";
        $validatedStr = $str;
        try {
            $validator->_assertUnicodeNoSpecialCharsAndNotEmpty($validatedStr, "path", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedStr, $str);
        }
    }

    public function test_assertAsciiAndNotEmpty(): void {
        $validator = new Validator();
        $str = "a";
        $validatedStr = $str;
        $validator->_assertAsciiAndNotEmpty($validatedStr, "path", "key", null);
        $this->assertSame($validatedStr, $str);

        $str = "\x80";
        $validatedStr = $str;
        try {
            $validator->_assertAsciiAndNotEmpty($validatedStr, "path", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedStr, $str);
        }

        $str = "";
        $validatedStr = $str;
        try {
            $validator->_assertAsciiAndNotEmpty($validatedStr, "path", "key", null);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertSame($validatedStr, $str);
        }
    }
}
