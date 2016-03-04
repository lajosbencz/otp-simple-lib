<?php

namespace OtpSimple;

use InvalidArgumentException;
use OtpSimple\Enum\Currency;

class Merchant extends Object
{
    /** @var Security */
    private $_security;

    /** @var string */
    protected $_id = '';
    /** @var string */
    protected $_key = '';
    /** @var string|Currency */
    protected $_currency = Currency::__default;

    protected function _describeFields()
    {
        return [];
    }


    /**
     * Merchant constructor.
     * @param Currency|string $currency (optional)
     * @param string $id
     * @param string $key
     */
    public function __construct($currency, $id, $key)
    {
        $this->_security = new Security($key);
        $this->_id = $id;
        $this->_key = $key;
        $currency = (string)$currency;
        if(Currency::isValidName($currency)) {
            $this->_currency = $currency;
        }
        elseif(Currency::isValidValue($currency)) {
            $this->_currency = Currency::getString($currency);
        }
        else {
            throw new InvalidArgumentException('Invalid currency: '.$currency);
        }
    }

    public function describeFields()
    {
        return [
            'id' => ['set'=>false],
            'key' => ['set'=>false],
            'currency' => ['set'=>false],
        ];
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function getKey() {
        return $this->_key;
    }

    /**
     * @return Currency|string
     */
    public function getCurrency() {
        return $this->_currency;
    }

    /**
     * @param string|array $data
     * @param array $whiteList (optional)
     * @return string
     */
    public function hash($data, array $whiteList=[]) {
        return $this->_security->hash($data, $whiteList);
    }
}