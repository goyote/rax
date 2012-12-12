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
     * @param array  $delimiters
     *
     * @return string
     */
    public static function ucWords($str, $delimiters = array('-', '.', '_'))
    {
        $str[0] = strtoupper($str[0]);
        foreach ($delimiters as $delimiter) {
            if (false !== strpos($str, $delimiter)) {
                $str = implode($delimiter, array_map('ucfirst', explode($delimiter, $str)));
            }
        }

        return $str;
    }

    /**
     * @param string $str
     * @param bool   $firstUpper
     *
     * @return string
     */
    public static function toCamelcase($str, $firstUpper = false)
    {
        $str = str_replace(' ', '', ucwords(preg_replace('/[-\s_.]+/', ' ', strtolower($str))));

        if (!$firstUpper) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }

    /**
     * @param string $str
     * @param array  $delimiters
     *
     * @return string
     */
    public static function toUndercase($str, $delimiters = array('-', '.', ' '))
    {
        return str_replace($delimiters, '_', $str);
    }

    /**
     * @param string $str
     * @param array  $delimiters
     *
     * @return string
     */
    public static function toHyphen($str, $delimiters = array('_', '.', ' '))
    {
        return str_replace($delimiters, '-', $str);
    }

    /**
     * @param string $str
     * @param array  $delimiters
     *
     * @return string
     */
    public static function toHuman($str, $delimiters = array('_', '.', '-'))
    {

        return str_replace($delimiters, ' ', $str);
    }
}
