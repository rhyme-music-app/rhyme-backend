<?php
namespace App\Utils;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class AlgoliaUtils
{
    private static ?SearchClient $client = null;

    private static ?SearchIndex $songIndex = null;

    public static function saveSong(array $songInfo): void
    {
        self::assertInitialized();
        // To conform with Algolia, we rename `id` to `objectID`, and also convert it to string.
        $songInfo['objectID'] = (string) $songInfo['id'];
        unset($songInfo['id']);
        self::$songIndex->saveObjects([ $songInfo ]);
    }

    public static function saveManySongs(array $songInfoList): void
    {
        self::assertInitialized();
        // To conform with Algolia, we rename `id` to `objectID`, and also convert it to string.
        for ($i = 0; $i < sizeof($songInfoList); ++$i) {
            $songInfo = $songInfoList[$i];
            $songInfo['objectID'] = (string) $songInfo['id'];
            unset($songInfo['id']);
            $songInfoList[$i] = $songInfo;
        }
        self::$songIndex->saveObjects($songInfoList);
    }

    public static function saveAllSongs(): void
    {
        self::assertInitialized();

        $songFieldsExceptId = [
            'name', 'image_link', 'audio_link',
            'added_at', 'updated_at', 'added_by', 'updated_by',
            'streams'
        ];
        $stmt = DatabaseConnection::prepare('SELECT
                id, ' . implode(', ', $songFieldsExceptId) . '
            FROM songs;'
        );

        $stmt->execute();
        $batch = [];
        foreach ($stmt->fetchAll() as $row) {
            $songInfo = [
                'objectID' => (string) $row['id'],
            ];
            foreach ($songFieldsExceptId as $field) {
                $songInfo[$field] = $row[$field];
            }
            $batch[] = $songInfo;
        }

        self::$songIndex->saveObjects($batch);
    }

    public static function deleteSong(string $songId): void
    {
        self::assertInitialized();
        self::$songIndex->deleteObjects([ $songId ]);
    }

    public static function deleteAllSongs(): void
    {
        self::assertInitialized();
        self::$songIndex->clearObjects();
    }

    private static function assertInitialized(): void
    {
        if (self::$client !== null) {
            return;
        }

        $ALGOLIA_APP_ID = self::readMandatoryEnv('ALGOLIA_APP_ID');
        $ALGOLIA_WRITE_KEY = self::readMandatoryEnv('ALGOLIA_WRITE_KEY');

        self::$client = SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_WRITE_KEY);
        self::$songIndex = self::$client->initIndex('rhyme_songs');
        self::$songIndex->setSettings([
            'searchableAttributes' => [
                'name',
            ],
            'ranking' => [
                'asc(name)',
                'desc(streams)',
            ],
        ]);
    }

    private static function readMandatoryEnv(string $envName): string
    {
        if (!isset($_ENV[$envName]) || !$_ENV[$envName]) {
            throw new HttpException(500, "`$envName` environment variable not configured. FOR ADMINS: Please look at .env file, and also CONTRIBUTING.md to complete the setup process.");
        }
        return $_ENV[$envName];
    }
}
