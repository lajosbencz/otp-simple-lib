<?php

namespace OtpSimple\Enum;

use OtpSimple\Enum;

class TransactionStatus extends Enum
{
    const __default = self::INIT;

    //The transaction has been created in the SimplePay system (start)
    const INIT = 'INIT';
    // Timeout in INIT status (timeout)
    const TIMEOUT = 'TIMEOUT';
    // Payment cancelled on the payment page, or the customer leaves the payment page, or closes the browser (cancel)
    const CANCELLED = 'CANCELLED';
    // Authorisation failed (fail)
    const NOT_AUTHORIZED = 'NOTAUTHORISED';
    // Ongoing payment, after the “Pay” button is pushed
    const IN_PAYMENT = 'INPAYMENT';
    // Ongoing examination, while the fraud detection is running
    const IN_FRAUD = 'INFRAUD';
    // Successful authorisation after the card details have been provided (success)
    const AUTHORIZED = 'AUTHORISED';
    // Fraud suspected (back)
    const FRAUD = 'FRAUD';
    // Blocked amount reversed (two-step) (finish)
    const REVERSED = 'REVERSED';
    // Refunded (partially or completely) (refund)
    const REFUND = 'REFUND';
    // Successful, completed transaction (ipn, finish)
    const FINISHED = 'FINISHED';

    public static function isSuccess($value)
    {
        $value = (string)$value;
        return
            $value == self::FINISHED ||
            $value == self::REFUND ||
            $value == self::REVERSED;
    }
}
