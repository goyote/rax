<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Php
{
    /**
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
     * @param callable|mixed $value
     *
     * @return mixed
     */
    public static function value($value)
    {
        return ($value instanceof Closure) ? $value() : $value;
    }

    /**
     * @param string $var
     *
     * @return string
     */
    public static function getType($var)
    {
        return is_object($var) ? get_class($var) : gettype($var);
    }
}
