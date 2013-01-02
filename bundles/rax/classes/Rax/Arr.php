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
     * array_unshift() for associative arrays. Prepends an key=>value pair to the
     * beginning of an array.
     *
     *     $arr = array('foo' => 'bar');
     *
     *     Arr::unshift($arr, 'key', 'value'); // array("key" => "value", "foo" => "bar")
     *     array_unshift($arr, 'value');       // array(0     => "value", "foo" => "bar")
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
     *
     * @return array|ArrayAccess
     */
    public static function set(&$array, $key, $value = null, $delimiter = null)
    {
        if (!static::isArray($array)) {
            throw new Error('Arr::set() expects parameter 1 to be an array or ArrayAccess object, %s given', Php::getType($array));
        }

        if (null === $delimiter) {
            $delimiter = Text::PATH_DELIMITER;
        }

        if (is_array($key)) {
            foreach ($key as $tmpKey => $tmpValue) {
                static::set($array, $tmpKey, $tmpValue, $delimiter);
            }
        } else {
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

        return $array;
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
    public static function get($array, $key = null, $default = null, $delimiter = null)
    {
        if (!static::isArray($array)) {
            throw new Error('Arr::get() expects parameter 1 to be an array or ArrayAccess object, %s given', Php::getType($array));
        }

        if (null === $delimiter) {
            $delimiter = Text::PATH_DELIMITER;
        }

        if (is_array($key)) {
            $tmp = array();
            foreach ($key as $tmpKey) {
                $tmp[$tmpKey] = static::get($array, $tmpKey, $default, $delimiter);
            }

            return $tmp;
        }

        if (null === $key) {
            return $array;
        }

        $keys = explode($delimiter, $key);

        foreach ($keys as $key) {
            if ((is_array($array) && array_key_exists($key, $array)) ||
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
    public static function delete(&$array, $key, $delimiter = null)
    {
        if (null === $delimiter) {
            $delimiter = Text::PATH_DELIMITER;
        }

        if (is_array($key)) {
            $tmp = array();
            foreach ($key as $tmpKey) {
                $tmp[$tmpKey] = static::delete($array, $tmpKey, $delimiter);
            }

            return $tmp;
        }

        $keys    = explode($delimiter, $key);
        $currKey = array_shift($keys);

        if ((!is_array($array) || !array_key_exists($currKey, $array)) &&
            (!$array instanceof ArrayAccess || !$array->offsetExists($currKey))
        ) {
            return false;
        }

        if (!empty($keys)) {
            $key = implode($delimiter, $keys);

            return static::delete($array[$currKey], $key, $delimiter);
        } else {
            unset($array[$currKey]);
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
    public static function has($array, $key, $delimiter = null)
    {
        if (!static::isArray($array)) {
            throw new Error('Arr::has() expects parameter 1 to be an array or ArrayAccess object, %s given', Php::getType($array));
        }

        if (null === $delimiter) {
            $delimiter = Text::PATH_DELIMITER;
        }

        if (is_array($key)) {
            foreach ($key as $tmpKey) {
                if (!static::has($array, $tmpKey, $delimiter)) {
                    return false;
                }
            }
        } else {
            $keys = explode($delimiter, $key);

            foreach ($keys as $key) {
                if ((is_array($array) && array_key_exists($key, $array)) ||
                    ($array instanceof ArrayAccess && $array->offsetExists($key))
                ) {
                    $array = $array[$key];
                } else {
                    return false;
                }
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
