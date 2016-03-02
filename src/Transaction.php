<?php

namespace OtpSimple;

use ArrayAccess;
use InvalidArgumentException;

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
     * @param array $data
     * @return array
     */
    abstract protected function _nameData($data=[]);

    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->setDefaults();
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
    public function isFieldHash($name) {
        return $this->describeField($name, 'hash') ? true : false;
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
     * @param string $name
     * @return string
     */
    public function getFieldRename($name) {
        return $this->describeField($name, 'rename');
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
     * @return array
     */
    public function getHashFields() {
        static $fields;
        if(!$fields) {
            $fields = [];
            foreach($this->_getFields() as $k=>$v) {
                if(array_key_exists('hash', $v) && $v['hash']) {
                    $fields[] = $k;
                }
            }
        }
        return $fields;
    }

    /**
     * @param string|array $names
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
        $a = count($allow)>0;
        foreach($data as $k=>$v) {
            if($a && !in_array($k, $allow)) {
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
        if(!$this->isFieldValid($name)) {
            throw new InvalidArgumentException('Invalid field name: '.$name);
        }
        $value = str_replace(["'","\\","\""],'',$value);
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

    public function checkRequired()
    {
        $this->_missing = [];
        foreach($this->_getFields() as $field => $params) {
            if(array_key_exists('required',$params) && $params['required']) {
                if(!array_key_exists($field, $this->_data) || !isset($this->_data[$field])) {
                    $this->_missing[$field] = $field . ($this->isFieldArray($field)?'[]':'');
                }
            }
        }
        $this->_missing = array_values($this->_missing);
        return count($this->_missing)==0;
    }

    public function getMissing()
    {
        return $this->_missing;
    }

    public function getData() {
        return $this->_data;
    }

    public function processResponse($resp)
    {
        preg_match_all('/<EPAYMENT>(.*?)<\/EPAYMENT>/', $resp, $matches);
        $data = explode("|", $matches[1][0]);
        return $this->_nameData($data);
    }

    public function checkResponseHash($resp=[])
    {
        $hash = $resp['ORDER_HASH'];
        array_pop($resp);
        $calculated = $this->config->getMerchant()->hash($resp);
        if($hash == $calculated) {
            return true;
        }
        return false;
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetExists($offset)
    {
        return $this->isFieldValid($offset);
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
     */
    public function offsetSet($offset, $value)
    {
        $this->setField($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->setField($offset, '');
    }

}