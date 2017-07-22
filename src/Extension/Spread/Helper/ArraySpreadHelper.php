<?php

namespace Phx\Extension\Spread\Helper;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class ArraySpreadHelper
{
    /**
     * @param array $array
     * @param string $key
     * @param array $values
     */
    public static function spreadArray(array &$array, string $key, array $values)
    {
        $oldArray = $array;
        $array = [];

        while (false !== ($value = current($oldArray))) {
            if ($value === $key) {
                foreach ($values as $spreadKey => $spreadValue) {
                    self::addKeyValue($array, $spreadKey, $spreadValue);
                }
            } else {
                self::addKeyValue($array, key($oldArray), $value);
            }

            next($oldArray);
        }
    }

    /**
     * @param array $array
     * @param $key
     * @param $value
     */
    private static function addKeyValue(array &$array, $key, $value)
    {
        if ((int)$key === $key) {
            $array[] = $value;
        } else {
            $array[$key] = $value;
        }
    }
}
