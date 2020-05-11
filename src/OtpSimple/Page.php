<?php

namespace OtpSimple;


class Page extends Component
{
    public function __construct(?Container $container = null)
    {
        if ($container) {
            $this->setContainer($container);
        }
    }

    public function toArray(): array
    {
        return Util::objectToArray($this);
    }
}
