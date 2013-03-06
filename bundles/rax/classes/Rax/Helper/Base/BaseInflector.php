<?php

namespace Rax\Helper\Base;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012-2013 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseInflector
{
    /**
     * Characters that are treated like word delimiters.
     *
     * @var array|string
     */
    public static $ucWordsDelimiters = array('-', '.', '_');

    /**
     * Characters that will form part of a regex character class.
     *
     * @var array|string
     */
    public static $camelcaseDelimiters = array('-', '\s', '.', '_');

    /**
     * Characters that are treated like word delimiters.
     *
     * @var array|string
     */
    public static $underscoreDelimiters = array('-', '.', ' ');

    /**
     * Characters that are treated like word delimiters.
     *
     * @var array|string
     */
    public static $humanDelimiters = array('_', '.', '-');

    /**
     * Characters that are treated like word delimiters.
     *
     * @var array|string
     */
    public static $charDelimiters = array('_', '.', '-', ' ');

    /**
     * @param string $str
     * @param array  $delimiters
     *
     * @return string
     */
    public static function ucWords($str, $delimiters = null)
    {
        if (null === $delimiters) {
            $delimiters = static::$ucWordsDelimiters;
        }

        $str = ucwords($str);
        foreach ((array) $delimiters as $delimiter) {
            if (false !== strpos($str, $delimiter)) {
                $str = implode($delimiter, array_map('ucfirst', explode($delimiter, $str)));
            }
        }

        return $str;
    }

    /**
     * Transforms a string to camelCase.
     *
     * @param string $str
     *
     * @return string
     */
    public static function toCamel($str)
    {
        return lcfirst(static::toUpperCamel($str));
    }

    /**
     * Transforms a string to UpperCamelCase.
     *
     * @param string $str
     *
     * @return string
     */
    public static function toUpperCamel($str)
    {
        $str = preg_replace('#[-\s._]+#', ' ', strtolower($str));

        return str_replace(' ', '', ucwords($str));
    }

    /**
     * @param string $str
     * @param string $separator
     *
     * @return string
     */
    public static function unCamel($str, $separator = '-')
    {
        return preg_replace('#([a-z])([A-Z])#', '$1'.$separator.'$2', $str);
    }

    /**
     * @param string $str
     * @param array  $delimiters
     *
     * @return string
     */
    public static function toUnderscore($str, $delimiters = null)
    {
        if (null === $delimiters) {
            $delimiters = static::$underscoreDelimiters;
        }

        return str_replace((array) $delimiters, '_', $str);
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
     * @param array  $delimiters
     *
     * @return string
     */
    public static function toHuman($str, $delimiters = null)
    {
        if (null === $delimiters) {
            $delimiters = static::$humanDelimiters;
        }

        return str_replace((array) $delimiters, ' ', $str);
    }

    /**
     * @param string $char
     * @param string $str
     * @param array  $delimiters
     *
     * @return string
     */
    public static function to($char, $str, $delimiters = null)
    {
        if (null === $delimiters) {
            $delimiters = static::$charDelimiters;
        }

        return str_replace((array) $delimiters, $char, $str);
    }
}
