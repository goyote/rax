<?php

namespace Rax\Routing\Filter\Base;

use Rax\Http\Request;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseMethodRouteFilter
{
    /**
     * @param string  $value
     * @param Request $request
     *
     * @return bool
     */
    public function filter($value, Request $request)
    {
        return $request->isMethod(explode('|', $value));
    }
}
