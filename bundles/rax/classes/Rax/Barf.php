<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Barf extends Exception
{
    /**
     *
     *
     * @param string      $message
     * @param array|mixed $values
     * @param Exception   $previous
     */
    public function __construct($message, $values = null, Exception $previous = null)
    {
        $message = Text::embedValues($message, $values);

        parent::__construct($message, 0, $previous);
    }
}
