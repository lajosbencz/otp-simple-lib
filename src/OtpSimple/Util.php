<?php

namespace OtpSimple;


use ReflectionClass;
use ReflectionProperty;

final class Util
{
    public static function getServerRequestHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    public static function objectToArray($object): array
    {
        $a = [];
        foreach ($object as $k => $v) {
            if (is_object($v) || is_array($v)) {
                $a[$k] = self::objectToArray($v);
            } else {
                $a[$k] = $v;
            }
        }
        return $a;
    }

    public static function copyFromArray(&$object, $array): void
    {
        $ref = new ReflectionClass($object);
        foreach ($ref->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            $k = $prop->getName();
            if (array_key_exists($k, $array)) {
                $object->$k = $array[$k];
            }
        }
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function __constructor()
    {
    }
}
