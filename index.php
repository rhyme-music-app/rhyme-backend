<?php
namespace RhymeMusicApp\RhymeMusicApp;

require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/view_begin.php';

use RhymeMusicApp\RhymeMusicApp\lib\B;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rhyme</title>
</head>
<body>
    <?php
    echo B::$indexMessage;
    ?>
    <br>
    <?php
    if ($_ENV['ABC'] == '8') {
        echo 'Environment setup OK';
    }
    else {
        echo 'Environment setup wrong ; expect 8 but got ' . $_ENV['ABC'];
    }
    ?>
</body>
</html>
