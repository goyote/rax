<?php

/**
 * @package   Rax\Helper
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Text
{
    /**
     * Embeds values into a string using either sprintf or strtr.
     *
     *     $str = Text::embedValues('hello %s', 'world'); // "hello world"
     *     $str = Text::embedValues('%s %s', array('hello', 'world')); // "hello world"
     *     $str = Text::embedValues('{{greeting}} {{planet}}', array(
     *         '{{greeting}}' => 'hello',
     *         '{{planet}}'   => 'world',
     *     )); // "hello world"
     *
     * @param string      $str
     * @param array|mixed $values
     *
     * @return string
     */
    public static function embedValues($str, $values = null)
    {
        if (null === $values) {
            return $str;
        }

        $values = (array) $values;

        if (Arr::isAssociative($values)) {
            $str = strtr($str, $values);
        } else {
            array_unshift($values, $str);
            $str = call_user_func_array('sprintf', $values);
        }

        return $str;
    }
}
