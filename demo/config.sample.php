<?php

return new \OtpSimple\Config([
    'sandbox' => true,
    'debug' => true,
    'timeout' => 30,
    'method' => 'CCVISAMC',
    'currency' => 'HUF',
    'merchants' => [
        'HUF' => [
            'id' => '*****',
            'key' => '*****',
        ],
    ],
]);
