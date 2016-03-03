<?php

$cfg = include __DIR__.'/boot.php';

$timeout = new \OtpSimple\Transaction\PaymentTimeout($cfg);

dump($timeout->getData());
