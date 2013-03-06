<?php

/**
 * @package   Rax\Validator
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Validator_NotEmpty extends Validator
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function validate($value)
    {
        if ($value instanceof ArrayObject) {
            $value = $value->getArrayCopy();
        }

        if (empty($value) && ('0' !== $value)) {
            return false;
        }

        return true;
    }
}
