<?php

namespace OtpSimple;

use ArrayAccess;

/**
 * @property string $name
 * @property string $code
 * @property string $info
 * @property float $price
 * @property float $qty
 * @property float $vat
 */
class Product extends Object
{
    protected $_name;
    protected $_code;
    protected $_info = '';
    protected $_price;
    protected $_qty = 1;
    protected $_vat = 0;

    public function __construct(array $data=[])
    {
        foreach($data as $k=>$v) {
            if(!$this->_isField($k)) {
                continue;
            }
            $this->$k = $v;
        }
    }

    public function _describeFields()
    {
        return [
            'name' => ['type'=>'scalar'],
            'code' => ['type'=>'scalar'],
            'info' => ['type'=>'scalar'],
            'price' => ['type'=>'scalar'],
            'qty' => ['type'=>'scalar'],
            'vat' => ['type'=>'scalar'],
        ];
    }
}