<?php

namespace Rax\Helper\Base;

use ArrayAccess;
use ArrayObject;
use Rax\Mvc\Exception;
use Rax\Helper\Php;
use Rax\Helper\TextHelper;

/**
 * Helper functions for working with arrays.
 *
 * @package   Rax\Helper
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseArr
{
    /**
     * Checks if the parameter is an array or array like object.
     *
     *     // true
     *     ArrHelper::isArray(array());
     *     ArrHelper::isArray(new ArrayObject());
     *
     *     // false
     *     ArrHelper::isArray('a');
     *     ArrHelper::isArray(123);
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
     *     ArrHelper::isAssociative(array('a' => 'b')); // true
     *     ArrHelper::isAssociative(array('a'));        // false
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

        return ($array !== array_values($array));
    }

    /**
     * Checks if the parameter is a numeric array.
     *
     *     ArrHelper::isNumeric(array('a'));        // true
     *     ArrHelper::isNumeric(array('a' => 'b')); // false
     *
     * @param array|ArrayObject $array
     *
     * @return bool
     */
    public static function isNumeric($array)
    {
        if ($array instanceof ArrayObject) {
            $array = $array->getArrayCopy();
        }

        if (!is_array($array) || empty($array)) {
            return false;
        }

        return ($array === array_values($array));
    }

    /**
     * array_unshift() for associative arrays. Prepends a key=>value item to the
     * beginning of an array.
     *
     *     $array = array('b' => 'b');
     *
     *     ArrHelper::unshift($array, 'a', 'a'); // array("a" => "a", "b" => "b")
     *     array_unshift($array, 'a');           // array(0   => "a", "b" => "b")
     *
     * @param array|ArrayAccess  $array
     * @param string             $key
     * @param mixed              $value
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
     *     $array = array();
     *     ArrHelper::set($array, 'one.two.three', 'wut');
     *
     *     array(
     *         "one" => array(
     *             "two" => array(
     *                 "three" => "wut",
     *             )
     *         )
     *     )
     *
     * @throws Exception
     *
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param mixed             $value
     * @param string            $delimiter
     */
    public static function set(&$array, $key, $value = null, $delimiter = null)
    {
        if (!static::isArray($array)) {
            throw new Exception('ArrHelper::set() expects parameter 1 to be an array or ArrayAccess object, %s given', Php::getType($array));
        }

        if (null === $delimiter) {
            $delimiter = TextHelper::PATH_DELIMITER;
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
    }

    /**
     * Returns the value found in the array or array like object at the specified
     * index or dot notation path.
     *
     * This function also helps avoid the dreaded notice that's thrown when you
     * try to access an undefined index.
     *
     *     $array = array(
     *         'one' => array(
     *             'two' => 2,
     *         ),
     *         'three' => 3,
     *         'four'  => 4,
     *     );
     *
     *     ArrHelper::get($array, 'one');                  // array("two" => 2)
     *     ArrHelper::get($array, 'one.two');              // 2
     *     ArrHelper::get($array, array('three', 'four')); // array("three" => 3, "four" => 4)
     *
     * @throws Exception
     *
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param mixed             $default
     * @param string            $delimiter
     *
     * @return mixed
     */
    public static function get($array, $key = null, $default = null, $delimiter = null)
    {
        if (!static::isArray($array)) {
            throw new Exception('ArrHelper::get() expects parameter 1 to be an array or ArrayAccess object, %s given', Php::getType($array));
        }

        if (null === $delimiter) {
            $delimiter = TextHelper::PATH_DELIMITER;
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
     *     $array = array(
     *         'one' => array(
     *             'two'   => 2,
     *             'three' => 3,
     *         ),
     *     );
     *
     *     ArrHelper::delete($array, 'one.two');
     *
     *     array(
     *         "one" => array(
     *             "three" => 3,
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
            $delimiter = TextHelper::PATH_DELIMITER;
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
     * Checks if the key exists in the array; accepts dot notation.
     *
     *     $arr = array(
     *         'one' => array(
     *             'two'   => 2,
     *         ),
     *     );
     *
     *     ArrHelper::has($arr, 'one.two');   // true
     *     ArrHelper::has($arr, 'one.three'); // false
     *
     * @throws Exception
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
            throw new Exception('ArrHelper::has() expects parameter 1 to be an array or ArrayAccess object, %s given', Php::getType($array));
        }

        if (null === $delimiter) {
            $delimiter = TextHelper::PATH_DELIMITER;
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
