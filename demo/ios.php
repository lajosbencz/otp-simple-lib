<?php

$cfg = include __DIR__.'/boot.php';

try {
    $status = new \OtpSimple\Transaction\OrderStatus($cfg);
    $status->order_id = $cfg->getRequest('order_id');
    $status->send();
    dump($status->getData(),$status->isSuccess());
} catch(Exception $e) {
    echo $e->getMessage();
}

