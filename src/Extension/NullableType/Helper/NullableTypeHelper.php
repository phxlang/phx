<?php

namespace Phx\Extension\NullableType\Helper;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class NullableTypeHelper
{
    public static function isType($value, string $type): bool
    {
        if (true === self::isNonObjectType($type)) {
            return call_user_func('is_' . $type, $value);
        } elseif (true === is_object($value) && get_class($value) === $type) {
            return true;
        }

        return false;
    }

    public static function getType($value): string
    {
        if ($value === null) {
            return 'null';
        }

        return is_object($value) ? get_class($value) : gettype($value);
    }

    public static function isNonObjectType(string $type): bool
    {
        $nonObjectTyprs = [
            'string' => true, 'int' => true, 'array' => true, 'float' => true, 'object' => true, 'callable' => true
        ];

        return isset($nonObjectTyprs[$type]);
    }
}
