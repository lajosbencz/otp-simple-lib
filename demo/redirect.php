<?php

ob_start();

$cfg = include __DIR__.'/boot.php';

try {
    $back = new \OtpSimple\Transaction\PaymentRedirect($cfg);
    dump($back->getData(), $back->checkResponse());
} catch(Exception $e) {
    throw $e;
}
