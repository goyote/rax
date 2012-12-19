<?php

/**
 * Helpers functions for working with arrays.
 *
 * @package   Rax\Helper
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Arr
{
    /**
     * Checks if the parameter is an array or array like object.
     *
     *     Arr::isArray(array());           // true
     *     Arr::isArray(new ArrayObject()); // true
     *     Arr::isArray('lol');             // false
     *
     * @param array|ArrayAccess $array
     *
     * @return bool
     */
    public static function isArray($array)
    {
        return (is_array($array) || $array instanceof ArrayAccess);
    }

    /**
     * Checks if the parameter is an associative array.
     *
     *     Arr::isAssociative(array('foo' => 'bar')); // true
     *     Arr::isAssociative(array('foo'));          // false
     *
     * @param array|ArrayObject $array
     *
     * @return bool
     */
    public static function isAssociative($array)
    {
        if ($array instanceof ArrayObject) {
            $array = $array->getArrayCopy();
        }

        if (!is_array($array)) {
            return false;
        }

        $keys = array_keys($array);

        return ($keys !== array_keys($keys));
    }

    /**
     * Prepends an item to the beginning of an associative array.
     *
     *     $arr = array('one' => 'one');
     *
     *     Arr::unshift($arr, 'two', 'two'); // array('two' => 'two', 'one' => 'one')
     *     array_unshift($arr, 'two');       // array(0     => 'two', 'one' => 'one')
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function unshift(array &$array, $key, $value)
    {
        return ($array = array($key => $value) + $array);
    }

    /**
     * Sets a value on an array using dot notation.
     *
     *     $arr = array();
     *     Arr::set($arr, 'one.two.three', 'wut');
     *
     *     array(
     *         'one' => array(
     *             'two' => array(
     *                 'three' => 'wut'
     *             )
     *         )
     *     )
     *
     * @throws Error
     *
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param mixed             $value
     * @param string            $delimiter
     */
    public static function set(&$array, $key, $value = null, $delimiter = '.')
    {
        if (!static::isArray($array)) {
            throw new Error('First argument must be an array or array like object, %s given', Php::getType($array));
        }

        if (is_array($key)) {
            foreach ($key as $tempKey => $tempValue) {
                static::set($array, $tempKey, $tempValue, $delimiter);
            }

            return;
        }

        $keys = explode($delimiter, $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($array[$key]) || !static::isArray($array[$key])) {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

    /**
     * Returns the value found at the specified index. Avoids a notice if the
     * index does not exist. You can provide multiple indexes to retrieve.
     *
     *     $arr = array(
     *         'one' => array(
     *             'two' => 2,
     *         ),
     *         'three' => 3,
     *         'four'  => 4,
     *     );
     *
     *     Arr::get($arr, 'one');                  // array('two' => 2)
     *     Arr::get($arr, 'one.two');              // 2
     *     Arr::get($arr, array('three', 'four')); // array('three' => 3, 'four' => 4)
     *
     * @throws Error
     *
     * @param array|ArrayObject $array
     * @param array|string      $key
     * @param mixed             $default
     * @param string            $delimiter
     *
     * @return mixed
     */
    public static function get($array, $key = null, $default = null, $delimiter = '.')
    {
        if (!static::isArray($array)) {
            throw new Error('First argument must be an array or array like object, %s given', Php::getType($array));
        }

        if (is_array($key)) {
            $temp = array();
            foreach ($key as $tempKey) {
                $temp[$tempKey] = static::get($array, $tempKey, $default, $delimiter);
            }

            return $temp;
        }

        if (null === $key) {
            return $array;
        }

        $keys = explode($delimiter, $key);

        foreach ($keys as $key) {
            if (
                (is_array($array) && array_key_exists($key, $array)) ||
                ($array instanceof ArrayAccess && $array->offsetExists($key))
            ) {
                $array = $array[$key];
            } else {
                return Php::value($default);
            }
        }

        return $array;
    }

    /**
     * Unsets an array item using dot notation.
     *
     *     $arr = array(
     *         'one' => array(
     *             'two'   => 2,
     *             'three' => 3,
     *         ),
     *     );
     *
     *     Arr::delete($arr, 'one.two');
     *
     *     array(
     *         'one' => array(
     *             'three' => 3
     *         )
     *     )
     *
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param string            $delimiter
     *
     * @return array|bool
     */
    public static function delete(&$array, $key, $delimiter = '.')
    {
        if (is_array($key)) {
            $temp = array();
            foreach ($key as $tempKey) {
                $temp[$tempKey] = static::delete($array, $tempKey, $delimiter);
            }

            return $temp;
        }

        $keys       = explode($delimiter, $key);
        $currentKey = array_shift($keys);

        if (
            (!is_array($array) || !array_key_exists($currentKey, $array)) &&
            (!$array instanceof ArrayAccess || !$array->offsetExists($currentKey))
        ) {
            return false;
        }

        if (!empty($keys)) {
            $key = implode($delimiter, $keys);

            return static::delete($array[$currentKey], $key, $delimiter);
        } else {
            unset($array[$currentKey]);
        }

        return true;
    }

    /**
     * Checks if the key exists in the array (accepts dot notation.)
     *
     *     $arr = array(
     *         'one' => array(
     *             'two'   => 2,
     *         ),
     *     );
     *
     *     Arr::has($arr, 'one.two');   // true
     *     Arr::has($arr, 'one.three'); // false
     *
     * @throws Error
     *
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param string            $delimiter
     *
     * @return bool
     */
    public static function has($array, $key, $delimiter = '.')
    {
        if (!static::isArray($array)) {
            throw new Error('First argument must be an array or array like object, %s given', Php::getType($array));
        }

        if (is_array($key)) {
            foreach ($key as $tempKey) {
                if (!static::has($array, $tempKey, $delimiter)) {
                    return false;
                }
            }

            return true;
        }

        $keys = explode($delimiter, $key);

        foreach ($keys as $key) {
            if (
                (is_array($array) && array_key_exists($key, $array)) ||
                ($array instanceof ArrayAccess && $array->offsetExists($key))
            ) {
                $array = $array[$key];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public static function merge()
    {
        $result = array();

        for ($i = 0, $total = func_num_args(); $i < $total; $i++) {
            $arr   = func_get_arg($i);
            $assoc = static::isAssociative($arr);

            foreach ($arr as $key => $val) {
                if (isset($result[$key])) {
                    if (is_array($val) && is_array($result[$key])) {
                        if (static::isAssociative($val)) {
                            $result[$key] = static::merge($result[$key], $val);
                        } else {
                            $diff         = array_diff($val, $result[$key]);
                            $result[$key] = array_merge($result[$key], $diff);
                        }
                    } else {
                        if ($assoc) {
                            $result[$key] = $val;
                        } elseif (!in_array($val, $result, true)) {
                            $result[] = $val;
                        }
                    }
                } else {
                    $result[$key] = $val;
                }
            }
        }

        return $result;
    }
}
