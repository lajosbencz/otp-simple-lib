<?php

namespace OtpSimple;

use OtpSimple\Logger\Html;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * @property Config $config
 * @property LoggerInterface $log
 */
abstract class Base implements LoggerAwareInterface
{
    const VERSION_MAJOR = 0;
    const VERSION_MINOR = 1;
    const VERSION_REVISION = 0;
    const VERSION_BUILD = 0;

    /** @var Config */
    protected $_config;
    /** @var LoggerInterface */
    protected $_logger;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->_config = $config;
        $this->_logger = new Logger('otp-simple',[
            new Html,
        ]);
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
        if($this->_config->hasName($name)) {
            return $this->_config->offsetGet($name);
        }
        return null;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;
        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger() {
        return $this->_logger;
    }

    public function __get($name)
    {
        switch($name) {
            case 'log':
                return $this->getLogger();
            case 'config':
                return $this->getConfig();
        }
        return null;
    }

}
