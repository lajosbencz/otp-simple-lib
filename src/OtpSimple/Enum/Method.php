<?php

namespace OtpSimple\Enum;

use OtpSimple\Enum;

class Method extends Enum
{
    const __default = self::CARD;

    const CARD = 'CARD';
    const WIRE = 'WIRE';
}
