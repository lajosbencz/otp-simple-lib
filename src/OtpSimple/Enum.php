<?php

namespace OtpSimple;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

abstract class Enum
{
    const __default = null;

    private static $_constants = null;
    protected $_value = null;

    /**
     * @param mixed $initial_value
     * @throws ReflectionException
     */
    public function __construct($initial_value = null)
    {
        if (self::isValidValue($initial_value)) {
            $this->_value = $initial_value;
        } else {
            $this->_value = static::__default;
        }
    }

    /**
     * @param string $name
     * @param bool $strict
     * @return bool
     * @throws ReflectionException
     */
    public static function isValidName(string $name, bool $strict = false): bool
    {
        $constants = self::getConstants();
        if ($strict) {
            return array_key_exists($name, $constants);
        }
        static $keys;
        if (!$keys) {
            $keys = array_map('strtolower', array_keys($constants));
        }
        return in_array(strtolower($name), $keys);
    }

    /**
     * @param string $value
     * @return bool
     * @throws ReflectionException
     */
    public static function isValidValue(string $value): bool
    {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict = true);
    }

    /**
     * @param string $value
     * @return string
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public static function getString(string $value): string
    {
        $key = array_search($value, self::getConstants());
        if (!is_string($key)) {
            throw new InvalidArgumentException('could not find constant by value: ' . $value);
        }
        return $key;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    private static function getConstants(): array
    {
        if (!self::$_constants) {
            $reflect = new ReflectionClass(get_called_class());
            self::$_constants = $reflect->getConstants();
        }
        return self::$_constants;
    }

    public function __toString()
    {
        return (string)$this->_value;
    }
}
