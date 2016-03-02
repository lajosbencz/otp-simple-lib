<?php

namespace OtpSimple\Enum;

use OtpSimple\EnumAbstract;

class Currency extends EnumAbstract
{
    const __default = self::EUR;

    const EUR = 'EUR';
    const USD = 'USD';
    const HUF = 'HUF';
}
