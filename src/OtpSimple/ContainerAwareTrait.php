<?php

namespace OtpSimple;

use OtpSimple\Exception\ContainerException;
use Psr\Log\LoggerInterface;

/**
 * @property-read Config $config
 * @property-read LoggerInterface $log
 * @property-read Component\BrokerInterface $broker
 * @property-read Component\Security $security
 */
trait ContainerAwareTrait
{
    private $_container;

    public function getContainer(): Container
    {
        if (!$this->_container) {
            $this->_container = Container::getDefault();
        }
        return $this->_container;
    }

    public function setContainer(Container $container): void
    {
        $this->_container = $container;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function __get(string $name)
    {
        $container = $this->getContainer();
        if (!$container) {
            throw new ContainerException('no container specified');
        }
        return $container->get($name);
    }
}
