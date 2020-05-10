<?php

namespace OtpSimple\Enum;


use OtpSimple\Enum;

class RedirectStatus extends Enum
{
    const __default = self::CANCEL;

    // Successful card authorisation
    const SUCCESS = 'SUCCESS';
    // Card authorisation failed, or 3DS check was unsuccessful
    const FAIL = 'FAIL';
    // The customer cancels their payment on the payment page
    const CANCEL = 'CANCEL';
    // Authorisation failed
    const TIMEOUT = 'TIMEOUT';
}
