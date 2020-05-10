<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../test/bootstrap.php';

$config = new OtpSimple\Config([
    'sandbox' => true,
    'merchants' => [
        'HUF' => [
            'id' => getenv('MERCHANT_ID'),
            'key' => getenv('MERCHANT_KEY'),
        ],
    ],
]);

global $otpSimple;
$otpSimple = new OtpSimple\OtpSimple($config);
