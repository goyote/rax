<?php

/**
 *
 */
class Core_Arr
{
    /**
     * The path separator.
     *
     * @var string
     */
    protected static $delimiter = '.';

    /**
     * @static
     *
     * @param string $delimiter
     *
     * @return null|string
     */
    public static function delimiter($delimiter = null)
    {
        if (is_null($delimiter)) {
            return static::$delimiter;
        }

        static::$delimiter = $delimiter;
    }

    /**
     * @static
     *
     * @param array|ArrayObject $array
     *
     * @return bool
     */
    public static function isArray($array)
    {
        if (is_array($array)) {
            return true;
        }

        return ($array instanceof ArrayObject);
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
        if (!is_array($array)) {
            return false;
        }

        $keys = array_keys($array);

        return $keys !== array_keys($keys);
    }

    public static function set(&$array, $key, $value, $delimiter = null)
    {
        if (is_null($delimiter)) {
            $delimiter = static::delimiter();
        }

        $keys = explode($delimiter, $key);


    }

    /**
     * @static
     *
     * @param array|ArrayObject $array
     * @param string            $key
     * @param mixed             $default
     * @param string            $delimiter
     *
     * @return mixed
     */
    public static function get($array, $key, $default = null, $delimiter = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (is_null($delimiter)) {
            $delimiter = static::delimiter();
        }

        if (false === strpos($key, $delimiter)) {
            return $default;
        }

        $keys = explode($delimiter, $key);

        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * @static
     *
     * @param array|ArrayObject  $array
     * @param string             $key
     * @param string             $delimiter
     *
     * @return bool
     */
    public static function has($array, $key, $delimiter = null)
    {
        if (array_key_exists($key, $array)) {
            return true;
        }

        if (is_null($delimiter)) {
            $delimiter = static::delimiter();
        }

        if (false === strpos($key, $delimiter)) {
            return false;
        }

        $keys = explode($delimiter, $key);

        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return false;
            }
        }

        return true;
    }

    public static function delete()
    {
    }

    public static function flatten(array $array)
    {
        $flat = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flat += static::flatten($value);
            } else {
                $flat[$key] = $value;
            }
        }

        return $flat;
    }
}
