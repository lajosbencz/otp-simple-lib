<?php

namespace OtpSimple;


use InvalidArgumentException;
use OtpSimple\Config\LanguageTrait;
use OtpSimple\Config\Merchant;
use OtpSimple\Config\RedirectTimeoutTrait;
use OtpSimple\Config\RedirectUrlTrait;

class Config
{
    use LanguageTrait, RedirectUrlTrait, RedirectTimeoutTrait;

    #region Constants
    const SALT_LENGTH = 32;

    const URL_BASE_LIVE = 'https://secure.simplepay.hu/payment/v2';
    const URL_BASE_SANDBOX = 'https://sandbox.simplepay.hu/payment/v2';

    const HASH_ALGORITHM = 'sha384';
    const SDK_VERSION = 'SimplePay_PHP_SDK_2.0.9_200130';

    #endregion

    /** @var bool */
    public $sandbox = true;

    /** @var int */
    public $request_timeout = 30;

    /** @var Merchant[] */
    protected $_merchants = [];

    /** @var string */
    protected $_default_currency = '';

    public function __construct(array $config = [])
    {
        $config = array_merge([
            'sandbox' => true,
            'merchants' => [],
        ], $config);
        Util::copyFromArray($this, $config);
        foreach ($config['merchants'] as $currency => $merchant) {
            $this->addMerchant(new Merchant($merchant['id'], $merchant['key'], isset($merchant['currency']) ? $merchant['currency'] : $currency));
        }
        $this->setRedirectUrl('http://localhost:8080/example/back.php');
    }

    public function isSandbox(): bool
    {
        return $this->sandbox !== false;
    }

    public function getBaseUrl(): string
    {
        return $this->isSandbox() ? self::URL_BASE_SANDBOX : self::URL_BASE_LIVE;
    }

    public function getCurrencies(): array
    {
        return array_keys($this->_merchants);
    }

    public function addMerchant(Config\Merchant $merchant): self
    {
        $this->_merchants[$merchant->currency] = $merchant;
        if (!$this->_default_currency) {
            $this->_default_currency = $merchant->currency;
        }
        return $this;
    }

    public function clearMerchants(): self
    {
        $this->_merchants = [];
        return $this;
    }

    public function setDefaultCurrency(string $currency): self
    {
        if (!array_key_exists($currency, $this->_merchants)) {
            throw new InvalidArgumentException('invalid merchant currency: ' . $currency);
        }
        $this->_default_currency = $currency;
        return $this;
    }

    public function getDefaultCurrency(): string
    {
        if (!$this->_default_currency) {
            reset($this->_merchants);
            $this->_default_currency = key($this->_merchants);
        }
        return $this->_default_currency;
    }

    public function getMerchant(?string $currency = null): Config\Merchant
    {
        if (!$currency) {
            $currency = $this->getDefaultCurrency();
        }
        if (!array_key_exists($currency, $this->_merchants)) {
            throw new InvalidArgumentException('invalid merchant currency: ' . $currency);
        }
        return $this->_merchants[$currency];
    }
}
