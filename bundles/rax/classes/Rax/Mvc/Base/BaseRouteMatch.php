<?php

namespace Rax\Mvc\Base;

use Rax\Helper\Inflector;
use Rax\Mvc\RouteMatch;
use ReflectionMethod;
use Rax\Helper\Arr;
use Rax\Mvc\Exception;
use Rax\Mvc\Object;
use Rax\Mvc\Symbol;
use Symfony\Component\Finder\Finder;
use Rax\Mvc\Route;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseRouteMatch
{
    /**
     * @var Route
     */
    protected $route;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $controllerClassName;

    /**
     * @var string
     */
    protected $viewClassName;

    /**
     * @var string
     */
    protected $twigTemplateName;

    /**
     * @var string
     */
    protected $actionMethodName;

    /**
     * @throws Exception
     *
     * @param Route $route
     * @param array $params
     */
    public function __construct(Route $route, array $params)
    {
        $this->route  = $route;
        $this->params = $params;

        $controller = $route->getDefault('controller');

        if (false === strpos($controller, ':')) {
            throw new Exception('The route "controller" should be specified as e.g. "App\Controller\Default:index"');
        }

        list($controllerClass, $action)   = explode(':', $controller);
        list($namespace, $controllerName) = explode('\\Controller\\', $controllerClass);

        $this->controller          = strtolower(Inflector::unCamel($controllerName));
        $this->action              = strtolower(Inflector::unCamel($action));
        $this->namespace           = $namespace.'\\';
        $this->controllerClassName = $controllerClass.'Controller';
        $this->actionMethodName    = $action.'Action';
        $this->viewClassName       = $this->namespace.'View\\'.$controllerName.'\\'.ucfirst($action).'View';
    }

    /**
     * Sets the route.
     *
     * @param Route $route
     *
     * @return RouteMatch
     */
    public function setRoute(Route $route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Returns the route.
     *
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Sets the params.
     *
     * @param array $params
     *
     * @return RouteMatch
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Returns the params.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Returns a param.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }

    /**
     * Sets the controller id.
     *
     * @param string $controller
     *
     * @return RouteMatch
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Returns the controller id.
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Sets the action id.
     *
     * @param string $action
     *
     * @return RouteMatch
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Returns the action id.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Sets the namespace.
     *
     * @param string $namespace
     *
     * @return RouteMatch
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Returns the namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Returns the controller class.
     *
     * @return string
     */
    public function getControllerClassName()
    {
        return $this->controllerClassName;
    }

    /**
     * Returns the action method.
     *
     * @return string
     */
    public function getActionMethodName()
    {
        return $this->actionMethodName;
    }

    /**
     * @return string
     */
    public function getViewClassName()
    {
        return $this->viewClassName;
    }

    /**
     * @return string
     */
    public function getTwigTemplateName()
    {
        return $this->controller.'/'.$this->action.'.twig';
    }

    /**
     * @return array
     */
//    public function getActionMethodParameterValues()
//    {
//        $method = new ReflectionMethod($this->getActionMethodName());
//
//        $params = array();
//        foreach ($method->getParameters() as $param) {
//            $value = Arr::get($this->params, $param->getName());
//
//            if (is_null($value) && $param->isDefaultValueAvailable()) {
//                $value = $param->getDefaultValue();
//            }
//
//            $params[] = $value;
//        }
//
//        return $params;
//    }
}
