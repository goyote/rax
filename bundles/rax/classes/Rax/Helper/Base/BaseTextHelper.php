<?php

namespace Rax\Helper\Base;

use Rax\Helper\ArrHelper;

/**
 * Helper functions for working with strings.
 *
 * @package   Rax\Helper
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseTextHelper
{
    /**
     * Delimiter used in a path to separate words.
     *
     * @var string
     */
    const PATH_DELIMITER = '.';

    /**
     * Embeds values into a string using either sprintf or strtr.
     *
     *     // "hello world"
     *     $str = TextHelper::embedValues('hello %s', 'world');
     *     $str = TextHelper::embedValues('%s %s', array('hello', 'world'));
     *     $str = TextHelper::embedValues('{{greeting}} {{planet}}', array(
     *         '{{greeting}}' => 'hello',
     *         '{{planet}}'   => 'world',
     *     ));
     *
     * @param string       $str
     * @param array|string $values
     *
     * @return string
     */
    public static function embedValues($str, $values = null)
    {
        if (null === $values) {
            return $str;
        }

        $values = (array) $values;

        if (ArrHelper::isAssociative($values)) {
            $str = strtr($str, $values);
        } else {
            array_unshift($values, $str);
            $str = call_user_func_array('sprintf', $values);
        }

        return $str;
    }
}
