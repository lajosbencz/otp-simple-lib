<?php

namespace OtpSimple;

interface RequestInterface
{
    /**
     * @param string $url
     * @param array $query (optional)
     * @return $this
     */
    function setUrl($url, array $query=[]);

    /**
     * @param array $data
     * @return $this
     */
    function setData(array $data);

    /**
     * @return string
     */
    function send();
}