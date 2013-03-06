<?php

namespace Rax\Helper\Base;

use Closure;
use Rax\Mvc\Exception;

/**
 * Helper functions for working with PHP.
 *
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BasePhp
{
    /**
     * Loads a file within an empty scope and returns the output.
     *
     *     // e.g. file holding a configuration array
     *     return array(...);
     *
     *     $array = PhpHelper::load($file);
     *
     * @throws Exception
     *
     * @param string $file
     *
     * @return mixed
     */
    public static function load($file)
    {
        if (!is_array($array = require $file)) {
            throw new Exception('%s does not return an array', $file);
        }

        return $array;
    }

    /**
     * @param Closure|mixed $value
     *
     * @return mixed
     */
    public static function value($value)
    {
        return ($value instanceof Closure) ? $value() : $value;
    }

    /**
     * Returns the data type of the variable.
     *
     * In the case of an object, the name of the class is returned.
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

    /**
     * Returns the result of var_export() with the coding style adjusted.
     *
     * @param mixed $data
     *
     * @return string
     */
    public static function varExport($data)
    {
        return strtr(var_export($data, true), array(
            '  '      => '    ',
            'array (' => 'array(',
        ));
    }
}
