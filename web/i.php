<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

function get($array, $key, $default = null, $delimiter = '.')
{
    if (array_key_exists($key, $array)) {
        return $array[$key];
    }

    if (false === strpos($key, $delimiter)) {
        return $default;
    }

    if (is_array($key)) {
        $return = array();

        foreach ($key as $k) {
            $return[$k] = get($array, $k, $default);
        }

        return $return;
    }

    $keys = explode($delimiter, $key);

    foreach ($keys as $key) {
        if (is_array($array) && array_key_exists($key, $array)) {
            $array = $array[$key];
        } else {
            return $default;
        }
    }

    return $array;
}

$a = array('foo' => 'bar', 'lol' => 'baz', 'xss' => 'hoersljl');
//$b = new ArrayObject($a);

var_dump(get($a, array('foo', 'lol')));
