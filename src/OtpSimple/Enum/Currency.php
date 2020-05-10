<?php

namespace OtpSimple\Enum;

use OtpSimple\Enum;

class Currency extends Enum
{
    const __default = self::EUR;

    const EUR = 'EUR';
    const USD = 'USD';
    const HUF = 'HUF';
}
