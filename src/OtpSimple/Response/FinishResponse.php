<?php

namespace OtpSimple\Response;


use OtpSimple\Request;
use OtpSimple\Response;

class FinishResponse extends Response
{
    /**
     * @return Request\FinishRequest
     */
    public function getRequest(): Request
    {
        return parent::getRequest();
    }

    public function process(array $data): void
    {
        parent::process($data);
    }
}
