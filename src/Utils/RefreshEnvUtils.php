<?php
namespace App\Utils;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshEnvUtils
{
    public static function execute(array $newValues, OutputInterface $output): int
    {
        // https://stackoverflow.com/a/32307974/13680015
        $path = __DIR__ . '/../../.env';

        $oldEnv = $_ENV;

        if (file_exists($path)) {
            // Actually, .env must always exist ; otherwise, Symfony would
            // reject to execute anything.
            $content = file_get_contents($path);
            if ($content === false) {
                $output->writeln("Error: Cannot read file .env");
                return Command::FAILURE;
            }

            $changeSecret = $newValues['changesecret'];
            if ($changeSecret === false) {
                // Default behavior: APP_SECRET will be created if it hasn't existed ;
                // otherwise it will be left unchanged.
                if (!isset($oldEnv['APP_SECRET']) || empty($oldEnv['APP_SECRET'])) {
                    // The secret hasn't existed yet, so we make one.
                    $changeSecret = true;
                }
            }

            self::replaceEnv($content, 'APP_ENV', $oldEnv['APP_ENV'] ?? '', $newValues['activeenv'] ?? 'dev');

            if ($changeSecret) self::replaceEnv($content, 'APP_SECRET', $oldEnv['APP_SECRET'] ?? '', self::generate_APP_SECRET());

            self::replaceEnv($content, 'CORS_ALLOW_ORIGIN', $oldEnv['CORS_ALLOW_ORIGIN'] ?? '', $oldEnv['CORS_ALLOW_ORIGIN'] ?? '\'^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$\'');

            self::replaceEnv($content, 'ALGOLIA_APP_ID', $oldEnv['ALGOLIA_APP_ID'] ?? '', $oldEnv['ALGOLIA_APP_ID'] ?? '');

            self::replaceEnv($content, 'ALGOLIA_WRITE_KEY', $oldEnv['ALGOLIA_WRITE_KEY'] ?? '', $oldEnv['ALGOLIA_WRITE_KEY'] ?? '');

            self::replaceEnv($content, 'ALGOLIA_INDEX', $oldEnv['ALGOLIA_INDEX'] ?? '', $oldEnv['ALGOLIA_INDEX'] ?? '');

            self::replaceEnv($content, 'DATABASE_HOST', $oldEnv['DATABASE_HOST'] ?? '', $newValues['dbhost']);

            self::replaceEnv($content, 'DATABASE_PORT', $oldEnv['DATABASE_PORT'] ?? '', $newValues['dbport']);

            self::replaceEnv($content, 'DATABASE_NAME', $oldEnv['DATABASE_NAME'] ?? '', $newValues['dbname']);

            self::replaceEnv($content, 'DATABASE_USER', $oldEnv['DATABASE_USER'] ?? '', $newValues['dbuser']);

            self::replaceEnv($content, 'DATABASE_PASS', $oldEnv['DATABASE_PASS'] ?? '', $newValues['dbpass']);

            self::replaceEnv($content, 'TIME_ZONE', $oldEnv['TIME_ZONE'] ?? '', $newValues['tz'] ?? 'Asia/Ho_Chi_Minh');

            if (false == file_put_contents($path, $content)) {
                $output->writeln("Error: Cannot write to file .env");
                return Command::FAILURE;
            }
            return Command::SUCCESS;
        }
        else {
            return Command::FAILURE;
        }
    }

    protected static function replaceEnv(string &$content, string $varName, string $oldVarValue, string $newVarValue): void
    {
        $content = str_replace(
            // Find what
            "$varName=$oldVarValue",
            // Replace with
            "$varName=$newVarValue",
            // Original content
            $content
        );
        if (false === strstr($content, $varName)) {
            // Variable not exists before, so we have to add it.
            $N = strlen($content);
            if ($N == 0 || ($N > 0 && $content[$N - 1] === "\n")) {
                // No need to add trailing newline before adding new variable.
            }
            else {
                $content .= "\n";
            }
            $content .= "$varName=$newVarValue\n";
        }
    }

    protected static function generate_APP_SECRET(): string
    {
        // https://stackoverflow.com/a/11449627/13680015
        // https://stackoverflow.com/questions/11449577/why-is-base64-encode-adding-a-slash-in-the-result#comment40679686_11449627
        $APP_SECRET = str_replace(
            ['+', '/'],
            ['-', '_'],
            base64_encode(random_bytes(48))
        );
        // About the number 48 above:
        // I want the secret to have exactly 64 bytes, so if I make a secret using
        // base64_encode, I need to encode a random string of length 48 since:
        //                       4n/3 = 64 => n = 48.
        // https://stackoverflow.com/a/13378842/13680015

        return $APP_SECRET;
    }
}
