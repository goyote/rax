<?php

/**
 *
 */
class Core_Arr
{
    /**
     * @static
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
     * @static
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
     * @static
     *
     * @param array|ArrayAccess $array
     * @param string            $key
     * @param mixed             $value
     * @param string            $delimiter
     *
     * @throws InvalidArgumentException
     */
    public static function set(&$array, $key, $value, $delimiter = '.')
    {
        if (!static::isArray($array)) {
            throw new InvalidArgumentException(sprintf('%s expects parameter 1 to be an array or ArrayAccess object, %s given', __METHOD__, gettype($array)));
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                static::set($array, $k, $v, $delimiter);
            }

            return;
        }

        $keys = explode($delimiter, $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!array_key_exists($key, $array) || !static::isArray($array[$key])) {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

    /**
     * @static
     *
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param mixed             $default
     * @param string            $delimiter
     *
     * @throws InvalidArgumentException
     * @return mixed
     */
    public static function get($array, $key, $default = null, $delimiter = '.')
    {
        if (!static::isArray($array)) {
            throw new InvalidArgumentException(sprintf('%s expects parameter 1 to be an array or ArrayAccess object, %s given', __METHOD__, gettype($array)));
        }

        if (static::isArray($key)) {
            $return = array();

            foreach ($key as $k) {
                $return[$k] = static::get($array, $k, $default, $delimiter);
            }

            return $return;
        }

        $keys = explode($delimiter, $key);

        foreach ($keys as $key) {
            if (static::isArray($array) && array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return Kernel::value($default);
            }
        }

        return $array;
    }

    /**
     * @static
     *
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param string            $delimiter
     *
     * @throws InvalidArgumentException
     * @return bool
     */
    public static function has($array, $key, $delimiter = '.')
    {
        if (!static::isArray($array)) {
            throw new InvalidArgumentException(sprintf('%s expects parameter 1 to be an array or ArrayAccess object, %s given', __METHOD__, gettype($array)));
        }

        if (static::isArray($key)) {
            foreach ($key as $k) {
                if (!static::has($array, $k, $delimiter)) {
                    return false;
                }
            }

            return true;
        }

        $keys = explode($delimiter, $key);

        foreach ($keys as $key) {
            if (static::isArray($array) && array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * @static
     *
     * @param array|ArrayAccess $array
     * @param array|string      $key
     * @param string            $delimiter
     *
     * @throws InvalidArgumentException
     * @return array|bool
     */
    public static function delete(&$array, $key, $delimiter = '.')
    {
        if (!static::isArray($array)) {
            throw new InvalidArgumentException(sprintf('%s expects parameter 1 to be an array or ArrayAccess object, %s given', __METHOD__, gettype($array)));
        }

        if (is_array($key)) {
            $return = array();

            foreach ($key as $k) {
                $return[$k] = static::delete($array, $k, $delimiter);
            }

            return $return;
        }

        $keys       = explode($delimiter, $key);
        $currentKey = array_shift($keys);

        if (!static::isArray($array) || !array_key_exists($currentKey, $array)) {
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
     * @static
     *
     * @param array $array
     *
     * @return array
     */
    public static function flatten(array $array)
    {
        $return = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return += static::flatten($value);
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     * @static
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     *
     * @return int
     */
    public static function unshift(array &$array, $key, $value)
    {
        $array = array($key => $value) + $array;

        return count($array);
    }
}
