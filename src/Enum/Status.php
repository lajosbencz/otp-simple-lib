<?php

namespace OtpSimple\Enum;

use OtpSimple\EnumAbstract;

class Status extends EnumAbstract
{
    const __default = 'NOT_FOUND';

    const NOT_FOUND = 'NOT_FOUND';
    const WAITING_PAYMENT = 'WAITING_PAYMENT';
    const CARD_NOTAUTHORIZED = 'CARD_NOTAUTHORIZED';
    const IN_PROGRESS = 'IN_PROGRESS';
    const FRAUD = 'FRAUD';
    const INVALID = 'INVALID';
    const TEST = 'TEST';
    const REVERSED = 'REVERSED';

    const PAYMENT_AUTHORIZED = 'PAYMENT_AUTHORIZED';
    const COMPLETE = 'COMPLETE';
    const REFUND = 'REFUND';
    const PAYMENT_RECEIVED = 'PAYMENT_RECEIVED';
    const CASH = 'CASH';

    public static function isSuccess($value) {
        $value = (string)$value;
        return
            $value == self::PAYMENT_AUTHORIZED ||
            $value == self::COMPLETE ||
            $value == self::REFUND ||
            $value == self::REVERSED ||
            $value == self::PAYMENT_RECEIVED ||
            $value == self::CASH
        ;
    }
}