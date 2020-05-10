<?php

namespace OtpSimple\Entity;


use OtpSimple\Util;

class Transaction
{
    public $salt = '';
    public $merchant = '';
    public $orderRef = '';
    public $transactionId = '';
    public $status = '';
    public $resultCode = 0;
    public $remainingTotal = 0.;
    public $paymentDate = '';
    public $finishDate = '';
    public $method = '';

    public function __construct(array $data = [])
    {
        Util::copyFromArray($this, $data);
    }
}
