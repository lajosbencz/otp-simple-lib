<?php

require_once __DIR__ . '/bootstrap.php';

global $otpSimple;

$start = $otpSimple->start();
$start->orderRef = 'test-' . uniqid(time() . '-');
$start->email = 'foo@bar.co';
$start->total = 123;
$start->twoStep = false;
$start->setRedirectUrl('http://localhost/back.php');
$res = $start->send();

include 'header.php';

echo '<a href="' . $res->paymentUrl . '">Pay!</a>';

include 'footer.php';
