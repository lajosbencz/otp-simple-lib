<?php

namespace OtpSimple;

use ArrayAccess;

abstract class RequestAbstract implements RequestInterface, ArrayAccess
{
    protected $_url;
    protected $_data = [];

    public function setUrl($url, array $query=[]) {
        if(count($query)>0) {
            $query = '?'.http_build_query($query, '', '&');
        } else {
            $query = '';
        }
        $this->_url = $url.$query;
        return $this;
    }

    function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_data);
    }

    public function offsetGet($offset)
    {
        if($this->offsetExists($offset)) {
            return $this->_data[$offset];
        }
        return null;
    }

    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if(!$this->offsetExists($offset)) {
            unset($this->_data[$offset]);
        }
    }

    abstract function send();

}