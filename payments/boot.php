<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

function dump($var) {
    static $cli;
    if(!isset($cli)) {
        $cli = strpos(strtolower(php_sapi_name()),'cli')!==false;
    }
    if(!$cli) {
        echo '<pre>';
    }
    foreach(func_get_args() as $a) {
        var_dump($a);
    }
    if(!$cli) {
        echo '</pre>';
    }
}

/** @var \OtpSimple\Config $config */
$config = include __DIR__ . '/config.php';
$config->setUrlTimeout('http'.($_SERVER['HTTPS']?'s':'').'://'.$_SERVER['SERVER_NAME'].'/payments/timeout.php');
$config->setUrlBack('http'.($_SERVER['HTTPS']?'s':'').'://'.$_SERVER['SERVER_NAME'].'/payments/back.php');
return $config;