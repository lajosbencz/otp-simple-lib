<?php

namespace OtpSimple;

use ArrayAccess;

/**
 * @property string $name
 * @property string $group
 * @property string $code
 * @property string $info
 * @property float $price
 * @property float $qty
 * @property float $vat
 * @property string $ver
 */
class Product implements ArrayAccess
{
    protected static $_keys = ['name','group','code','info','price','qty','vat','ver'];

    protected $_name;
    protected $_group = '01';
    protected $_code;
    protected $_info = '';
    protected $_price;
    protected $_qty = 1;
    protected $_vat = 0;
    protected $_ver = '01';

    public function __construct(array $data=[], $strict=false)
    {
        foreach($data as $k=>$v) {
            if(!$this->offsetExists($k) && $strict) {
                continue;
            }
            $this->offsetSet($k,$v);
        }
    }

    public function offsetExists($offset)
    {
        return in_array($offset, self::$_keys);
    }

    public function offsetGet($offset)
    {
        if(!$this->offsetExists($offset)) {
            throw new Exception('Invalid product property: '.$offset);
        }
        return $this->{'_'.$offset};
    }

    public function offsetSet($offset, $value)
    {
        if(!$this->offsetExists($offset)) {
            throw new Exception('Invalid product property: '.$offset);
        }
        $value = str_replace(["'","\\","\""],'',$value);
        $this->{'_'.$offset} = $value;
    }

    public function offsetUnset($offset)
    {
        if(!$this->offsetExists($offset)) {
            throw new Exception('Invalid product property: '.$offset);
        }
        $this->{'_'.$offset} = '';
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

}