<?php

namespace Rax\Twig\Base;

use Rax\Http\Request;
use Rax\Http\Response;
use Rax\Mvc\Kernel;
use Rax\Twig\View;
use Twig_Environment;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012-2013 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseView
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @param Request          $request
     * @param Twig_Environment $twig
     */
    public function __construct(Request $request, Twig_Environment $twig)
    {
        $this->request = $request;
        $this->twig    = $twig;
    }

    /**
     * @param array|string $name
     * @param mixed        $value
     *
     * @return View
     */
    public function set($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->$key = $value;
            }
        } else {
            $this->$name = $value;
        }

        return $this;
    }

    /**
     * Returns a string of css classes for the body element.
     *
     * @param array|string $append
     *
     * @return string
     */
    public function getBodyCssClasses($append = array())
    {
        $classes   = array();
//        $classes[] = $this->request->getController();
//        $classes[] = $this->request->getAction();

        return implode(' ', array_merge((array) $append, $classes));
    }
}
