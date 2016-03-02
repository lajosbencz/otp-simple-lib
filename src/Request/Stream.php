<?php

namespace OtpSimple\Request;

use OtpSimple\RequestAbstract;
use OtpSimple\RequestInterface;

class Stream extends RequestAbstract implements RequestInterface
{
    public function send()
    {
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' =>
                    "Accept-language: en\r\n".
                    "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($this->_data, '', '&')
            ));
        $context = stream_context_create($options);
        return file_get_contents($this->_url, false, $context);
    }
}