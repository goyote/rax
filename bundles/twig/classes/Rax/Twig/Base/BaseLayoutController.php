<?php

namespace Rax\Twig\Base;

use Rax\Mvc\Controller;

/**
 * @package   Rax\Twig
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseLayoutController extends Controller
{
    /**
     * @var bool
     */
    protected $autoRender = true;

    /**
     * @var \Rax\Twig\View
     */
    protected $view;

    /**
     *
     */
    public function before()
    {
        if (!$this->autoRender) {
            return;
        }

        $class      = $this->request->getMatchedRoute()->getViewClassName();
        $this->view = new $class($this->request, $this->response, $this->kernel);
    }

    /**
     *
     */
    public function after()
    {
        if (!$this->autoRender) {
            return;
        }

        $this->response->setContent($this->view->render());
    }
}
