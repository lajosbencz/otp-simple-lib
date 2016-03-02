<?php

namespace OtpSimple;

use ArrayAccess;
use InvalidArgumentException;
use OtpSimple\Enum\Currency;

class Merchant implements ArrayAccess
{
    private static $_keys = ['id', 'key', 'currency'];

    /** @var string */
    protected $_id = '';
    /** @var string */
    protected $_key = '';
    /** @var string|Currency */
    protected $_currency = Currency::__default;

    /**
     * Merchant constructor.
     * @param string $id
     * @param string $key
     * @param Currency|string $currency (optional)
     */
    public function __construct($id, $key, $currency = Currency::__default)
    {
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

    public function hash(array $data) {
        $hash = '';
        foreach($data as $field) {
            if(is_array($field)) {
                //throw new Exception('No multi-dimensional array allowed!');
                foreach($field as $v) {
                    $v = stripslashes($v);
                    $hash .= strlen($v) . $v;
                }
            } else {
                $field = stripslashes($field);
                $hash .= strlen($field) . $field;
            }
        }
        return Security::hmac($this->_key, $hash);
    }

    public function getId() {
        return $this->_id;
    }

    public function getKey() {
        return $this->_key;
    }

    public function getCurrency() {
        return $this->_currency;
    }

    public function offsetExists($offset)
    {
        return in_array($offset, self::$_keys);
    }

    public function offsetGet($offset)
    {
        if(!$this->offsetExists($offset)) {
            throw new Exception('Invalid offset for merchant: '.$offset);
        }
        return $this->{'_'.$offset};
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception('Merchant object is ready-only!');
    }

    public function offsetUnset($offset)
    {
        throw new Exception('Merchant object is ready-only!');
    }

    public function toArray() {
        return [
            'id' => $this->getId(),
            'key' => $this->getKey(),
            'currency' => $this->getCurrency(),
        ];
    }
}