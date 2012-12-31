<?php

/**
 *
 */
class Rax_Controller
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
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @param Request  $request
     * @param Response $response
     * @param Kernel   $kernel
     */
    public function __construct(Request $request, Response $response, Kernel $kernel)
    {
        $this->request  = $request;
        $this->response = $response;
        $this->kernel   = $kernel;
    }

    /**
     * @param string $connectionName
     * @param bool   $new
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager($connectionName = null, $new = false)
    {
        return $this->kernel->getEntityManager($connectionName, $new);
    }

    /**
     *
     */
    public function before()
    {
        $class = $this->request->getMatchedRoute()->getViewClassName();
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
