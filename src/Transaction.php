<?php

namespace OtpSimple;

use ArrayAccess;
use InvalidArgumentException;
use OtpSimple\Exception\FieldOverflowException;

abstract class Transaction extends Base
{
    protected static $_reverseMap = false;

    /** @var array */
    private $_missing = [];

    /**
     * @param array $map
     * @param array $old
     * @param bool $swap (optional)
     * @return array
     */
    public static function renameFields(array $map, array &$old, $swap=false) {
        $new = [];
        if($swap) {
            $map = array_flip($map);
        }
        foreach($map as $to=>$from) {
            $new[$to] = $old[$from];
        }
        return $new;
    }

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->setDefaults();
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws FieldOverflowException
     */
    public function __set($name, $value)
    {
        $l = $this->getFieldLength($name);
        if($l > 0 && is_string($value) && strlen($value) > $l) {
            throw new FieldOverflowException('Field '.$name.' maximum length is '.$l);
        }
        parent::__set($name, $value);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isFieldRequired($name) {
        return $this->_describeField($name, 'required') ? true : false;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isFieldArray($name) {
        return $this->_describeField($name, 'type') == 'array';
    }

    /**
     * @param $name
     * @return int
     */
    public function getFieldLength($name) {
        return $this->_describeField($name, 'length');
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getFieldDefault($name) {
        return $this->_describeField($name, 'default');
    }

    public function getFieldsMap() {
        $map = [];
        foreach($this->_describeFields()?:[] as $k=>$v) {
            if(array_key_exists('name', $v)) {
                $map[$k] = $v['name'];
            }
        }
        return $map;
    }

    /**
     * @return bool
     */
    public function checkRequired()
    {
        $this->_missing = [];
        foreach($this->_describeFields() as $name => $params) {
            if(array_key_exists('required', $params) && $params['required']) {
                if(!$this->__isset($name)) {
                    $this->_missing[$name] = $name . ($this->isFieldArray($name)?'[]':'');
                }
            }
        }
        $this->_missing = array_values($this->_missing);
        return count($this->_missing)<1;
    }

    /**
     * @return array
     */
    public function getMissing()
    {
        return $this->_missing;
    }

    /**
     * @return array
     */
    public function getData() {
        if(static::$_reverseMap) {
            return $this->_data;
        }
        return self::renameFields($this->getFieldsMap(), $this->_data, true);
    }

}