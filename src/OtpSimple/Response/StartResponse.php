<?php

namespace OtpSimple\Response;


use OtpSimple\Response;

class StartResponse extends Response
{
    public $salt = '';
    public $merchant = '';
    public $orderRef = '';
    public $currency = '';
    public $transactionId = '';
    public $timeout = '';
    public $total = 0.;
    public $paymentUrl = '';
}
