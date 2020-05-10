<?php

namespace OtpSimple\Request;


use OtpSimple\Request;
use OtpSimple\Response;

class FinishRequest extends Request
{
    public $originalTotal = 0;
    public $approvedTotal = 0;

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
        return '/finish';
    }

}
