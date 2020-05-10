<?php

namespace OtpSimple\Exception;


use OtpSimple\Exception;
use Throwable;

class VerifySignatureException extends Exception
{
    public function __construct($message = "failed to verify signature", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
