<?php

ob_start();

$cfg = include __DIR__.'/boot.php';

dump($_REQUEST);

try {
    $back = new \OtpSimple\Transaction\PaymentBack($cfg);
    dump($back->checkResponse());
} catch(Exception $e) {
    throw $e;
}
