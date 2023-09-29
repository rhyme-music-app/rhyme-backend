<?php
namespace RhymeMusicApp\RhymeMusicApp\api;

require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/api_begin.php';

use RhymeMusicApp\RhymeMusicApp\lib\A;
use \PDO;
use PDOException;

$a = new A();
echo $a->sampleString; // should be 5

echo '<br>';

if (env('ABC') == '8') {
    echo 'Environment setup OK';
}
else {
    echo 'Environment setup wrong ; expect 8 but got ' . env('ABC');
    die();
}
?>

<br>

<?php
$mysqlHostName = env('MYSQL_HOST_NAME');
$mysqlPort = env('MYSQL_PORT');
$mysqlDatabase = env('MYSQL_DATABASE');
$mysqlUser = env('MYSQL_USER');
$mysqlPassword = env('MYSQL_PASSWORD');

$connection = new PDO("mysql:host=$mysqlHostName;port=$mysqlPort;dbname=$mysqlDatabase", $mysqlUser, $mysqlPassword);

$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $connection->exec("CREATE TABLE `users` (
        `id` INT NOT NULL,
        `email` VARCHAR(256) CHARACTER SET 'ascii' COLLATE 'ascii_general_ci' NOT NULL,
        `name` TEXT NOT NULL,
        `password_hash` VARCHAR(256) CHARACTER SET 'ascii' COLLATE 'ascii_general_ci' NOT NULL,
        `is_admin` TINYINT NOT NULL DEFAULT 0,
        `deleted` TINYINT NOT NULL DEFAULT 0,

        PRIMARY KEY (`id`),
        UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE
    ) ENGINE = InnoDB;");
} catch (PDOException $e) {
    echo 'The table may have already been created.<br>';
    die();
}

$connection->exec("INSERT INTO `users` (`id`, `email`, `name`, `password_hash`, `is_admin`, `deleted`)
VALUES (
    1,
    'fake@email.com',
    'Faker',
    'sdufhalsdfsd1g2dzf313dzf13zd123f',
    FALSE,
    FALSE
);");

$firstRow = $connection->query("SELECT * FROM `users`")->fetch();

if ($firstRow['email'] == 'fake@email.com') {
    echo 'Database setup OK.';
}
else {
    echo 'Database setup or SQL queries wrong.';
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/api_end.php';
