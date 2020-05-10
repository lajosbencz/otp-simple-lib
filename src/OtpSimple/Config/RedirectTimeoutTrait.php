<?php

namespace OtpSimple\Config;


trait RedirectTimeoutTrait
{
    private $_redirectTimeout = 300;

    public function getRedirectTimeout(): int
    {
        return $this->_redirectTimeout;
    }

    public function setRedirectTimeout(int $redirectTimeout): self
    {
        $this->_redirectTimeout = max(60, $redirectTimeout);
        return $this;
    }

}
