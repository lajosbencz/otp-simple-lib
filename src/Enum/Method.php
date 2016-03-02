<?php

namespace OtpSimple\Enum;

use OtpSimple\EnumAbstract;

class Method extends EnumAbstract
{
    const __default = self::CCVISAMC;

    const CCVISAMC = 'CCVISAMC';
    const WIRE = 'WIRE';
    const AUTOMODE = '';
}
