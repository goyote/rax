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
    public static $hyphenDelimiters = array('_', '.', ' ');

    /**
     * Characters that are treated like word delimiters.
     *
     * @var array|string
     */
    public static $humanDelimiters = array('_', '.', '-');

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
     * @param string       $str
     * @param bool         $firstUpper
     * @param array|string $delimiters
     *
     * @return string
     */
    public static function toCamelcase($str, $firstUpper = false, $delimiters = null)
    {
        if (null === $delimiters) {
            $delimiters = static::$camelcaseDelimiters;
        }

        $str = str_replace(' ', '', ucwords(preg_replace('/['.implode('', (array) $delimiters).']+/', ' ', strtolower($str))));

        if (!$firstUpper) {
            $str = lcfirst($str);
        }

        return $str;
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
     * @param array  $delimiters
     *
     * @return string
     */
    public static function toHyphen($str, $delimiters = null)
    {
        if (null === $delimiters) {
            $delimiters = static::$hyphenDelimiters;
        }

        return str_replace((array) $delimiters, '-', $str);
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
}
