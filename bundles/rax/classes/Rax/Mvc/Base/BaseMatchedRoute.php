<?php

namespace Rax\Mvc\Base;

use Rax\Helper\Inflector;
use Rax\Routing\Route;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseMatchedRoute
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
    protected $namespace;

    /**
     * @var string
     */
    protected $controllerClassName;

    /**
     * @var string
     */
    protected $actionMethodName;

    /**
     * @var string
     */
    protected $viewClassName;

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @param Route $route
     * @param array $params
     */
    public function __construct(Route $route, array $params)
    {
        $this->route  = $route;
        $this->params = $params;
        $this->parse($route);
    }

    /**
     * @param Route $route
     */
    public function parse(Route $route)
    {
        list($controller, $action)        = explode(':', $route->getController());
        list($namespace, $controllerName) = explode('\\Controller\\', $controller);

        $this->namespace           = $namespace;
        $this->controllerClassName = $controller.'Controller';
        $this->actionMethodName    = $action.'Action';
        $this->viewClassName       = $namespace.'\\'.'View\\'.$controllerName.'\\'.ucfirst($action).'View';
        $this->templateName        = strtolower(str_replace('\\', '/', $namespace).'/'.Inflector::unCamel($controllerName).'/'.Inflector::unCamel($action));
    }

    /**
     * Gets the matched route.
     *
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Gets the matched route parameters.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Gets a matched route parameter.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        return array_key_exists($key, $this->params) ? $this->params[$key] : $default;
    }

    /**
     * Gets the controller's namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Gets the controller class.
     *
     * @return string
     */
    public function getControllerClassName()
    {
        return $this->controllerClassName;
    }

    /**
     * Gets the action method.
     *
     * @return string
     */
    public function getActionMethodName()
    {
        return $this->actionMethodName;
    }

    /**
     * Gets the view class.
     *
     * @return string
     */
    public function getViewClassName()
    {
        return $this->viewClassName;
    }

    /**
     * Gets the template path.
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }
}
