<?php

namespace OtpSimple\Exception;


use OtpSimple\Exception;
use Throwable;

class InvalidSignatureException extends Exception
{
    public function __construct($message = "invalid signature received", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
