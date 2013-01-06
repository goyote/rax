<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
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
     * @return Doctrine\ORM\EntityManager
     */
    public function getManager($connectionName = null, $new = false)
    {
        return $this->kernel->getEntityManager($connectionName, $new);
    }

    /**
     * @param string $entityName
     * @param string $connectionName
     * @param bool   $new
     *
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository($entityName, $connectionName = null, $new = false)
    {
        return $this->getManager($connectionName, $new)->getRepository($entityName);
    }

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
