<?php

namespace OtpSimple\Response;


use OtpSimple\Request;
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

    /**
     * @return Request\RefundRequest
     */
    public function getRequest(): Request
    {
        return parent::getRequest();
    }
}
