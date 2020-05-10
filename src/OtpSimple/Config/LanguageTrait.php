<?php

namespace OtpSimple\Config;


trait LanguageTrait
{
    private $_language = 'EN';

    public function getLanguage(): string
    {
        return $this->_language;
    }

    public function setLanguage(string $language): self
    {
        $this->_language = $language;
        return $this;
    }
}
