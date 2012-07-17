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
     * @param null $delimiter
     *
     * @return string|null
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
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * @static
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     * @param string $delimiter
     *
     * @return mixed
     */
    public static function getFromPath(array $array, $key, $default = null, $delimiter = null)
    {
        if (is_null($delimiter)) {
            $delimiter = static::delimiter();
        }

        $keys = explode($delimiter, $key);

        foreach ($keys as $key) {
            if (null === ($value = static::get($array, $key))) {
                return $default;
            } else {
                $array = $value;
            }
        }

        return $value;
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
