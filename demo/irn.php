<?php

ob_start();

$cfg = include __DIR__.'/boot.php';

try {
    $back = new \OtpSimple\Transaction\RefundNotification($cfg);
    /*
    if($back->checkResponse()) {
        $back->confirm();
    }
    */
    dump($back->getData());
} catch(Exception $e) {
    echo $e->getMessage();
}

$out = ob_get_contents();

file_put_contents(dirname(__DIR__).'/log/irn.txt',$out.PHP_EOL,FILE_APPEND);
