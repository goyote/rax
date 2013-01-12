<?php

namespace Rax\Helper\Base;

use Closure;

/**
 * Helper functions missing in PHP.
 *
 * @package   Rax
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BasePhpHelper
{
    /**
     * Loads a file within an empty scope and returns the output.
     *
     *     // The $file's contents
     *     return array();
     *
     *     $array = PhpHelper::load($file);
     *
     * @param string $file
     *
     * @return mixed
     */
    public static function load($file)
    {
        /** @noinspection PhpIncludeInspection */
        return include $file;
    }

    /**
     * Returns the parameter untouched if it's not a closure, otherwise it
     * returns the result of the callback.
     *
     *     // ArrHelper uses PhpHelper::value() to return the default value
     *     ArrHelper::get($array, 'undefined_key', function() use($foo) {
     *         // Do something else
     *     });
     *
     * @param Closure|mixed $value
     *
     * @return mixed
     */
    public static function value($value)
    {
        return ($value instanceof Closure) ? $value() : $value;
    }

    /**
     * Returns the data type of the parameter.
     *
     * In the case of an object, it returns the class name instead of the string
     * "object", which is more useful when debugging a script.
     *
     *     gettype(new Exception());            // "object"
     *     PhpHelper::getType(new Exception()); // "Exception"
     *
     * @param string $var
     *
     * @return string
     */
    public static function getType($var)
    {
        return is_object($var) ? get_class($var) : gettype($var);
    }
}
