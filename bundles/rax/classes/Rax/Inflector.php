<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Inflector
{
    /**
     * @param string $str
     *
     * @return string
     */
    public static function ucWords($str)
    {
        foreach (array('-', '.', '_') as $delimiter) {
            $str = implode($delimiter, array_map('ucfirst', explode($delimiter, $str)));
        }

        return $str;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function toCamelcase($str)
    {
        $str = 'x'.strtolower($str);
        $str = ucwords(preg_replace('/[-\s_.]+/', ' ', $str));

        return substr(str_replace(' ', '', $str), 1);
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function toUndercase($str)
    {
        return str_replace(array('-', '.', ' '), '_', $str);
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function toHyphen($str)
    {
        return str_replace(array('_', '.', ' '), '-', $str);
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function toHuman($str)
    {

        return str_replace(array('_', '.', '-'), ' ', $str);
    }
}
