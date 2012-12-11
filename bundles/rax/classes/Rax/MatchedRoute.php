<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_MatchedRoute extends Object
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
    protected $controllerClass;

    /**
     * @var string
     */
    protected $actionMethod;

    /**
     * @var array
     */
    protected $params;

    /**
     * @throws Barf
     *
     * @param string $name
     * @param array  $params
     */
    public function __construct($name, array $params)
    {
        if (empty($params['controller'])) {
            throw new Barf('Route "%s" is missing the "controller" segment', $name);
        }

        if (empty($params['action'])) {
            throw new Barf('Route "%s" is missing the "action" segment', $name);
        }

        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getController()
    {
        if (null === $this->controller) {
            $this->controller = Inflector::toHyphen(strtolower($this->params['controller']));
        }

        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        if (null === $this->action) {
            $this->action = Inflector::toHyphen(strtolower($this->params['action']));
        }

        return $this->action;
    }

    /**
     * @return string
     */
    public function getControllerClass()
    {
        if (null === $this->controllerClass) {
            $this->controllerClass = 'Controller_'.Inflector::ucWords(Inflector::toUndercase($this->params['controller']));
        }

        return $this->controllerClass;
    }

    /**
     * @return string
     */
    public function getActionMethod()
    {
        if (null === $this->actionMethod) {
            $this->actionMethod = Inflector::toCamelcase($this->params['action']).'Action';
        }

        return $this->actionMethod;
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return array
     */
    public function getMethodArguments(ReflectionMethod $method)
    {
        $parameters = array();
        foreach ($method->getParameters() as $parameter) {
            $value = Arr::get($this->params, $parameter->getName());
            if (null === $value && $parameter->isDefaultValueAvailable()) {
                $value = $parameter->getDefaultValue();
            }
            $parameters[] = $value;
        }

        return $parameters;
    }
}
