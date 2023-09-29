<?php

function env(string $key, $default = null) {
    $value = $_ENV[$key];
    if (!isset($value) || empty($value)) {
        return $default;
    }
    return $value;
}
