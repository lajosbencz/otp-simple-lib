<?php

namespace OtpSimple;

use InvalidArgumentException;
use OtpSimple\Logger\Html;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * @property Config $config
 * @property LoggerInterface $log
 */
abstract class Base extends Object implements LoggerAwareInterface
{
    const VERSION_MAJOR = 0;
    const VERSION_MINOR = 1;
    const VERSION_REVISION = 0;
    const VERSION_BUILD = 0;

    /** @var LoggerInterface */
    private $_log;
    /** @var Config */
    private $_config;

    /**
     * @param array|Config $config
     * @param LoggerInterface $logger (optional)
     */
    public function __construct($config, LoggerInterface $logger=null)
    {
        if(is_array($config)) {
            $config = new Config($config);
        }
        if(!$logger) {
            $logger = new Logger('otp-simple',[
                new Html,
            ]);
        }
        $this->_config = $config;
        $this->setLogger($logger);
    }

    public function getVersion() {
        static $v;
        if(!$v) {
            $v = self::VERSION_MAJOR.'.'.self::VERSION_MINOR.'.'.self::VERSION_REVISION.'.'.self::VERSION_BUILD;
        }
        return $v;
    }

    public function getConfig($name=null) {
        if($name === null) {
            return $this->_config;
        }
        if($this->_config->$name) {
            return $this->_config->$name;
        }
        return null;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->_log = $logger;
        return $this;
    }

    public function __get($name)
    {
        if($name=='log') {
            return $this->_log;
        }
        if($name=='config') {
            return $this->_config;
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if($name=='log' || $name=='config') {
            throw new InvalidArgumentException('Read-only field: '.$name);
        }
        parent::__set($name, $value);
    }

}
