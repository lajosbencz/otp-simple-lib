<?php

namespace OtpSimple\Enum;

use OtpSimple\Enum;

class FormMode extends Enum
{
    const __default = self::SUBMIT;

    const SUBMIT = 0;
    const LINK = 1;
    const AUTO = 2;
}
