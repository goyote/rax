<?php

namespace Rax\Routing\Filter\Base;

use Rax\Mvc\ServerMode;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseServerModeRouteFilter
{
    /**
     * @param string     $value
     * @param ServerMode $serverMode
     *
     * @return bool
     */
    public function filter($value, ServerMode $serverMode)
    {
        return $serverMode->is(explode('|', $value));
    }
}
