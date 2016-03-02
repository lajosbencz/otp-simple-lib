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
}