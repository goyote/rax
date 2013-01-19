<?php

namespace Rax\Mvc\Base;

use Rax\Http\Request;
use Rax\Http\Response;
use Rax\Mvc\Kernel;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012-2013 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseController
{
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
     * @param string $entityName
     * @param string $connectionName
     * @param bool   $new
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository($entityName, $connectionName = null, $new = false)
    {
        return $this->getEntityManager($connectionName, $new)->getRepository($entityName);
    }
}
