<?php

namespace OtpSimple\Enum;

use OtpSimple\EnumAbstract;

class FormMode extends EnumAbstract
{
    const __default = self::SUBMIT;

    const SUBMIT = 0;
    const LINK = 1;
    const AUTO = 2;
}