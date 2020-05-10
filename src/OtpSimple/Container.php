<?php

namespace OtpSimple;


use OtpSimple\Exception\ContainerException;

class Container
{
    protected static $_default;
    protected $_definitions = [];
    protected $_resolved = [];

    public function __construct()
    {
        if (!self::getDefault()) {
            self::setDefault($this);
        }
    }

    public static function getDefault(): ?self
    {
        return self::$_default;
    }

    public static function setDefault(Container $container): void
    {
        self::$_default = $container;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->_definitions);
    }

    public function set(string $name, callable $definition, bool $shared = true): void
    {
        unset($this->_definitions[$name]);
        unset($this->_resolved[$name]);
        $this->_definitions[$name] = [
            $definition,
            $shared,
        ];
    }

    public function get(string $name)
    {
        if (!$this->has($name)) {
            throw new ContainerException('unknown service: ' . $name);
        }
        $shared = $this->_definitions[$name][1];
        if (!$shared) {
            $resolved = $this->_definitions[$name][0]($this);
            if ($resolved instanceof ContainerAwareInterface) {
                $resolved->setContainer($this);
            }
            return $resolved;
        }
        if (!isset($this->_resolved[$name])) {
            $resolved = $this->_definitions[$name][0]($this);
            if ($resolved instanceof ContainerAwareInterface) {
                $resolved->setContainer($this);
            }
            $this->_resolved[$name] = $resolved;
        }
        return $this->_resolved[$name];
    }
}
