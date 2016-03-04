<?php


namespace OtpSimple;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class Object
 * @package OtpSimple
 * @property array $data
 */
abstract class Object
{
    private $_protectedFields = null;
    private $_protectedField = null;
    private $_fields = null;
    private $_field = null;
    private $_setField = null;
    private $_getField = null;

    protected $_data = [];

    abstract protected function _describeFields();

    protected function _getFields() {
        if(!is_array($this->_fields))  {
            $this->_fields = array_keys($this->_describeFields()?:[]);
        }
        return $this->_fields;
    }

    protected  function _isField($name) {
        if(!is_array($this->_field)) {
            $this->_field = [];
        }
        if(!array_key_exists($name, $this->_field)) {
            $this->_field[$name] = in_array($name, $this->_getFields());
        }
        return $this->_field[$name];
    }

    protected  function _describeField($name, $attribute=null, $default=null) {
        if(!$this->_isField($name)) {
            throw new InvalidArgumentException('Invalid field name: '.$name);
        }
        $f = $this->_describeFields()?:[];
        if(!is_array($f[$name])) {
            $f = [];
        } else {
            $f = $f[$name];
        }
        if($attribute===null) {
            return $f;
        }
        if(array_key_exists($attribute, $f)) {
            return $f[$attribute];
        }
        return $default;
    }

    protected  function _isSetField($name) {
        if(!is_array($this->_setField)) {
            $this->_setField = [];
        }
        if(!array_key_exists($name, $this->_setField)) {
            $this->_setField[$name] = $this->_describeField($name, 'set', true);
        }
        return $this->_setField[$name];
    }

    protected  function _isGetField($name) {
        if(!is_array($this->_getField)) {
            $this->_getField = [];
        }
        if(!array_key_exists($name, $this->_getField)) {
            $this->_getField[$name] = $this->_describeField($name, 'get', true);
        }
        return $this->_getField[$name];
    }

    /**
     * @param array $data
     * @param array $allow (optional)
     * @return $this
     */
    public function setFields(array $data, array $allow=[]) {
        $this->_data = [];
        return $this->mergeFields($data, $allow);
    }

    /**
     * @param array $data
     * @param array $allow (optional)
     * @return $this
     */
    public function mergeFields(array $data, array $allow=[]) {
        if(!is_array($allow) || count($allow)<1) {
            $allow = $this->_getFields();
        }
        foreach($data as $k=>$v) {
            if(!in_array($k, $allow)) {
                continue;
            }
            $this->$k = $v;
        }
        return $this;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __get($name)
    {
        if(!$this->_isGetField($name)) {
            throw new Exception('Field is write-only: '.$name);
        }
        if(!$this->__isset($name)) {
            return null;
        }
        return $this->_data[$name];
    }

    public function __set($name, $value)
    {
        if(!$this->_isSetField($name)) {
            throw new Exception('Field is read-only: '.$name);
        }
        $this->_data[$name] = $value;
    }

    public function __unset($name)
    {
        unset($this->_data[$name]);
    }

    /**
     * @return array
     */
    public function toArray() {
        $array = [];
        foreach($this->_getFields() as $name) {
            $v = $this->$name;
            if($v===null) {
                continue;
            }
            if(is_a($this->$name, self::class)) {
                $v = $v->toArray();
            }
            $array[$name] = $v;
        }
        return $array;
    }
}
