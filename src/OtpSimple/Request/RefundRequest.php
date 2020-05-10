<?php

namespace OtpSimple\Request;


use OtpSimple\Request;
use OtpSimple\Response;

class RefundRequest extends Request
{
    public $refundTotal = 0;

    public $orderRef = '';
    public $transactionId = '';

    /**
     * @return Response\FinishResponse
     */
    public function send(): Response
    {
        return parent::send();
    }

    public function getApiUrl(): string
    {
        return '/refund';
    }

}
