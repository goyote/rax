<?php

/**
 * {@inheritDoc}
 */
class Rax_Controller_Layout extends Controller
{
    /**
     * @var bool
     */
    protected $autoRender = true;

    /**
     * @var View
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
