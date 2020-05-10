<?php

require_once __DIR__ . '/../vendor/autoload.php';

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (array_map('trim', explode("\n", file_get_contents($envFile))) as $env) {
        if (strlen($env) < 3 || strpos($env, '=') === false) {
            continue;
        }
        putenv($env);
    }
}

