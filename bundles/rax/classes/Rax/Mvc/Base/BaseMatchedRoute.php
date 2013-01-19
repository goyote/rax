<?php

namespace Rax\Mvc\Base;

use ReflectionMethod;
use Rax\Helper\ArrHelper;
use Rax\Mvc\Exception;
use Rax\Mvc\Object;
use Rax\Mvc\Symbol;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012-2013 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseMatchedRoute extends Object
{
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
     * @var array
     */
    protected $params;

    /**
     * @throws Exception
     *
     * @param string $name
     * @param array  $params
     */
    public function __construct($name, array $params)
    {
        if (empty($params['controller'])) {
            throw new Exception('Route "%s" is missing the "controller" segment', $name);
        }

        if (empty($params['action'])) {
            throw new Exception('Route "%s" is missing the "action" segment', $name);
        }

        $this->params = $params;
    }

    /**
     * Normalized string identifier for the controller. Use getControllerClassName()
     * to get the actual PHP class name.
     *
     * @return string
     */
    public function getController()
    {
        if (null === $this->controller) {
            $this->controller = Symbol::buildId($this->params['controller']);
        }

        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        if (null === $this->action) {
            $this->action = Symbol::buildId($this->params['action']);
        }

        return $this->action;
    }

    /**
     * @return string
     */
    public function getControllerClassName()
    {
        if (null === $this->controllerClassName) {
            $this->controllerClassName = Symbol::buildControllerClassName($this->params['controller']);
        }

        return $this->controllerClassName;
    }

    /**
     * @return string
     */
    public function getViewClassName()
    {
        if (null === $this->viewClassName) {
            $this->viewClassName = Symbol::buildViewClassName($this->params['controller'], $this->params['action']);
        }

        return $this->viewClassName;
    }

    /**
     * @return string
     */
    public function getActionMethodName()
    {
        if (null === $this->actionMethodName) {
            $this->actionMethodName = Symbol::buildActionMethodName($this->params['action']);
        }

        return $this->actionMethodName;
    }

    /**
     * @return string
     */
    public function getTwigTemplateName()
    {
        if (null === $this->twigTemplateName) {
            $this->twigTemplateName = Symbol::buildTwigTemplateName($this->params['controller'], $this->params['action']);
        }

        return $this->twigTemplateName;
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return array
     */
    public function getMethodArguments(ReflectionMethod $method)
    {
        $params = array();
        foreach ($method->getParameters() as $param) {
            $value = ArrHelper::get($this->params, $param->getName());
            if (null === $value && $param->isDefaultValueAvailable()) {
                $value = $param->getDefaultValue();
            }
            $params[] = $value;
        }

        return $params;
    }
}
