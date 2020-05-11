<?php

require_once __DIR__ . '/bootstrap.php';

$ipn = new OtpSimple\Page\IpnPage;

$ipn->log->debug('received IPN request: ', $ipn->toArray());

$ipn->confirm();
