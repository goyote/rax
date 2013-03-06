<?php

namespace Rax\Mvc\Base;

use Rax\Mvc\Container;
use Rax\Mvc\Exception;
use Rax\Mvc\Router;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Rax\Mvc\Kernel;
use PhpDriver;
use Rax\Data\Config;
use Rax\Http\Request;
use Rax\Http\Response;
use Rax\Mvc\Cfs;
use Rax\Mvc\ServerMode;
use Rax\Mvc\ServiceContainer;
use ReflectionClass;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseKernel
{
    /**
     * Current version number of the Rax PHP framework.
     *
     * @see http://semver.org/
     */
    const VERSION = '0.1.0';

    /**
     * @var ServiceContainer
     */
    public $service;

    /**
     * @var Cfs
     */
    protected $cfs;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var ServerMode
     */
    protected $serverMode;

    /**
     * @var Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * @var array
     */
    protected $entityManagers = array();

    /**
     * Returns a new chainable instance.
     *
     * @return Kernel
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Sets the Cfs.
     *
     * @param Cfs $cfs
     *
     * @return Kernel
     */
    public function setCfs(Cfs $cfs)
    {
        $this->cfs = $cfs;

        return $this;
    }

    /**
     * Returns the Cfs.
     *
     * @return Cfs
     */
    public function getCfs()
    {
        return $this->cfs;
    }

    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the request.
     *
     * @param Request $request
     *
     * @return Kernel
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Returns the request.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the router.
     *
     * @param Router $router
     *
     * @return Kernel
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Returns the router.
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    public function setService(ServiceContainer $service)
    {
        $this->service = $service;

        return $this;
    }

    public function getService()
    {
        return $this->service;
    }

    /**
     * Sets the ServerMode.
     *
     * @param ServerMode $serverMode
     *
     * @return Kernel
     */
    public function setServerMode(ServerMode $serverMode)
    {
        $this->serverMode = $serverMode;

        return $this;
    }

    /**
     * Returns the ServerMode.
     *
     * @return ServerMode
     */
    public function getServerMode()
    {
        return $this->serverMode;
    }

    /**
     * @throws Exception
     *
     * @return Response
     */
    public function process()
    {
        if (!$routeMatch = $this->router->match($this->request)) {
            // throw 404
            throw new Exception('todo throw 404');
        }
        $this->request->setRouteMatch($routeMatch);

        $response = new Response();

        $this->service->routeMatch = $routeMatch;
        $this->service->response = $response;

        $controller = $this->service->build($routeMatch->getControllerClassName());

        if (method_exists($controller, 'before')) {
            $this->service->callMethod($controller, 'before');
        }

        $this->service->callMethod($controller, $routeMatch->getActionMethodName(), $routeMatch->getParams());

        if (method_exists($controller, 'after')) {
            $this->service->callMethod($controller, 'after');
        }

        $response->setContent($this->service->view->render());

        return $response;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return 'UTF-8';
    }
}
