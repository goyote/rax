<?php

namespace Rax\Mvc\Base;

use Exception;
use Rax\Helper\TextHelper;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseException extends Exception
{
    /**
     * @param string      $message
     * @param array|mixed $values
     * @param Exception   $previous
     */
    public function __construct($message, $values = null, Exception $previous = null)
    {
        parent::__construct(TextHelper::embedValues($message, $values), 0, $previous);
    }
}
