<?php

namespace OtpSimple;

use ArrayAccess;
use InvalidArgumentException;
use OtpSimple\Exception\FieldOverflowException;

abstract class Transaction extends Base implements ArrayAccess
{
    /** @var array */
    protected $_missing = [];
    /** @var array */
    protected $_data = [];

    /**
     * @return array
     */
    abstract protected function _getFields();

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
     */
    public function __set($name, $value)
    {
        $this->setField($name, $value);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $value = parent::__get($name);
        if($value === null) {
            $value = $this->getField($name);
        }
        return $value;
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_data);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getField($offset);
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws FieldOverflowException
     */
    public function offsetSet($offset, $value)
    {
        $l = $this->getFieldLength($offset);
        if($l > 0 && strlen($value) > $l) {
            throw new FieldOverflowException('Field '.$offset.' value is longer than '.$l);
        }
        $this->setField($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->setField($offset, '');
    }

    /**
     * @param string $name
     * @param string $attribute (optional)
     * @return array
     */
    public function describeField($name, $attribute=null) {
        static $fields;
        if(!$fields) {
            $fields = $this->_getFields();
        }
        if($attribute === null) {
            if(array_key_exists($name, $fields)) {
                return $fields[$name];
            }
            return null;
        }
        if(!array_key_exists($name, $fields) || !array_key_exists($attribute, $fields[$name])) {
            return null;
        }
        return $fields[$name][$attribute];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isFieldValid($name) {
        return is_array($this->describeField($name));
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isFieldSet($name) {
        return isset($this->_data[$name]);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isFieldRequired($name) {
        return $this->describeField($name, 'required') ? true : false;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isFieldSimple($name) {
        return $this->describeField($name, 'type') == 'simple';
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isFieldArray($name) {
        return $this->describeField($name, 'type') == 'array';
    }

    /**
     * @param $name
     * @return int
     */
    public function getFieldLength($name) {
        return $this->describeField($name, 'length');
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getFieldDefault($name) {
        return $this->describeField($name, 'default');
    }

    /**
     * @return array
     */
    public function getValidFields() {
        static $fields;
        if(!$fields) {
            $fields = array_keys($this->_getFields());
        }
        return $fields;
    }

    /**
     * @param array $names (optional)
     * @return $this
     */
    public function setDefaults($names=null)
    {
        if(!$names) {
            $names = $this->getValidFields();
        }
        foreach($names as $name) {
            if(!$this->isFieldSimple($name)) {
                continue;
            }
            $def = $this->getFieldDefault($name);
            if($def===null) {
                continue;
            }
            $this->offsetSet($name, $def);
        }
        return $this;
    }

    /**
     * @param array $data
     * @param array $allow (optional)
     * @return $this
     */
    public function setFields(array $data, array $allow=null) {
        $this->_data = [];
        return $this->mergeFields($data, $allow);
    }

    /**
     * @param array $data
     * @param array $allow (optional)
     * @return $this
     */
    public function mergeFields(array $data, array $allow=null) {
        if(!is_array($allow) || count($allow)<1) {
            $allow = $this->getValidFields();
        }
        foreach($data as $k=>$v) {
            $k = strtolower($k);
            if(!in_array($k, $allow)) {
                continue;
            }
            $this->setField($k,$v);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setField($name, $value) {
        $name = strtolower($name);
        if(!$this->isFieldValid($name)) {
            throw new InvalidArgumentException('Invalid field name: '.$name);
        }
        if(is_string($value)) {
            $value = str_replace(["'", "\\", "\""], '', $value);
        }
        $this->_data[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getField($name) {
        if(!$this->isFieldValid($name)) {
            throw new InvalidArgumentException('Invalid field name: '.$name);
        }
        return $this->_data[$name];
    }

    /**
     * @return bool
     */
    public function checkRequired()
    {
        $this->_missing = [];
        foreach($this->_getFields() as $field => $params) {
            if(array_key_exists('required', $params) && $params['required']) {
                if(!array_key_exists($field, $this->_data) || !isset($this->_data[$field])) {
                    $this->_missing[$field] = $field . ($this->isFieldArray($field)?'[]':'');
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
        return $this->_data;
    }

}