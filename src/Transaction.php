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
    /** @var array */
    private $_renamed = [];

    /**
     * @param array $map
     * @param array $old
     * @return array
     */
    public static function renameFields(array &$map, array &$old) {
        $new = [];
        $idx = true;
        foreach($old as $ok=>&$mv) {
            if(!is_int($ok)) {
                $idx = false;
                break;
            }
        }
        if($idx) {
            foreach($old as $i=>&$ov) {
                $new[$i] = self::renameFields($map[0], $ov);
            }
        } else {
            foreach($old as $ok=>$ov) {
                if(array_key_exists($ok, $map)) {
                    $mv = $map[$ok];
                    if(is_array($mv)) {
                        if(is_array($ov)) {
                            if(count($mv) == 1) {
                                $k = array_keys($mv);
                                $k = $k[0];
                                $v = $mv[$k];
                                if (is_array($v)) {
                                    $new[$k] = self::renameFields($v, $ov);
                                    continue;
                                }
                            }
                            throw new InvalidArgumentException('Invalid $map');
                        }
                    } else {
                        $new[(string)$mv] = $ov;
                    }
                } else {
                    $new[$ok] = $ov;
                }
            }
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
        $this->$name = $value;
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

    public function getFieldsMap($reverse=false) {
        $map = [];
        foreach($this->_describeFields()?:[] as $k=>$v) {
            if(array_key_exists('name', $v)) {
                if($reverse) {
                    $map[$v['name']] = $k;
                } else {
                    $map[$k] = $v['name'];
                }
            }
        }
        return $map;
    }

    /**
     * @param array $names (optional)
     * @return $this
     */
    public function setDefaults($names=[])
    {
        if(!is_array($names) || count($names)<1) {
            $names = $this->_getFields();
        }
        foreach($names as $name) {
            if($this->isFieldArray($name)) {
                continue;
            }
            $def = $this->getFieldDefault($name);
            if($def===null) {
                continue;
            }
            $this->$name = $def;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function checkRequired()
    {
        $this->_missing = [];
        foreach($this->_describeFields() as $name => $params) {
            if(array_key_exists('required', $params) && $params['required']) {
                if(!$this->$name) {
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
        $r = [];
        foreach($this->_getFields() as $f) {
            $r[$f] = $this->$f;
        }
        return self::renameFields($this->getFieldsMap(static::$_reverseMap), $r);
    }

}