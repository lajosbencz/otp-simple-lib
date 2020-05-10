<?php

namespace OtpSimple\Config;


use InvalidArgumentException;

trait RedirectUrlTrait
{
    /** @var string[] */
    private $_redirectUrls = [];

    public function setRedirectUrl(string $url): self
    {
        $this->_redirectUrls = [
            'success' => $url,
            'fail' => $url,
            'cancel' => $url,
            'timeout' => $url,
        ];
        return $this;
    }

    public function setRedirectUrls(array $urls): self
    {
        $this->_redirectUrls = [
            'success' => $urls['success'],
            'fail' => $urls['fail'],
            'cancel' => $urls['cancel'],
            'timeout' => $urls['timeout'],
        ];
        return $this;
    }

    public function getRedirectUrl(string $type): string
    {
        if (!array_key_exists($type, $this->_redirectUrls)) {
            throw new InvalidArgumentException('invalid redirect url type: ' . $type);
        }
        return $this->_redirectUrls[$type];
    }

    public function getRedirectUrls(): array
    {
        return $this->_redirectUrls;
    }
}
