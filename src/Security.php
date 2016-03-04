<?php

namespace OtpSimple;

class Security
{
    const HMAC_BYTES = 64;

    public static function hmac($key, $data) {
        if (strlen($key) > self::HMAC_BYTES) {
            $key = pack("H*", md5($key));
        }
        $key = str_pad($key, self::HMAC_BYTES, chr(0x00));
        $pi = str_pad('', self::HMAC_BYTES, chr(0x36));
        $po = str_pad('', self::HMAC_BYTES, chr(0x5c));
        $ki = $key ^ $pi;
        $ko = $key ^ $po;
        return md5($ko.pack("H*", md5($ki.$data)));
    }

    public static function serialize(array $data, array $whiteList=[]) {
        $string = '';
        if(count($whiteList)>0) {
            $order = [];
            foreach($whiteList as $k=>$v) {
                if(is_array($v)) {
                    $order[] = $k;
                } else {
                    $order[] = $v;
                }
            }
        } else {
            $order = array_keys($data);
        }
        foreach($order as $o) {
            if(array_key_exists($o, $data)) {
                $d = $data[$o];
                if(is_array($d)) {
                    if(is_array($order[$o])) {
                        $string .= self::serialize($d, $order[$o]);
                    } else {
                        $string .= self::serialize($d);
                    }
                } else {
                    $string.= strlen($d).$d;
                }
            }
        }
        return $string;
    }

    protected $_key;

    public function __construct($key=null)
    {
        if($key) {
            $this->setKey($key);
        }
    }

    public function setKey($key) {
        $this->_key = $key;
        return $this;
    }

    /**
     * @param string|array $data
     * @param array $whiteList (optional)
     * @return string
     */
    public function hash($data, array $whiteList=[]) {
        if(is_array($data)) {
            $data = self::serialize($data, $whiteList);
        }
        return self::hmac($this->_key, $data);
    }

}