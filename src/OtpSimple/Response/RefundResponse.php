<?php

namespace OtpSimple\Response;


use OtpSimple\Response;

class RefundResponse extends Response
{
    public $salt = '';
    public $merchant = '';
    public $orderRef = '';
    public $currency = '';
    public $transactionId = '';
    public $refundTransactionId = '';
    public $refundTotal = 0.;
    public $remainingTotal = 0.;
}
