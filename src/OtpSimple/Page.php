<?php

namespace OtpSimple;


class Page extends Component
{
    public function toArray(): array
    {
        return Util::objectToArray($this);
    }
}
