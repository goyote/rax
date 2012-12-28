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
     * @throws Error
     *
     * @param string $name
     * @param array  $params
     */
    public function __construct($name, array $params)
    {
        if (empty($params['controller'])) {
            throw new Error('Route "%s" is missing the "controller" segment', $name);
        }

        if (empty($params['action'])) {
            throw new Error('Route "%s" is missing the "action" segment', $name);
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
    public function getControllerClassName()
    {
        if (null === $this->controllerClassName) {
            $this->controllerClassName = 'Controller_'.Inflector::ucWords(Inflector::toUnderscore($this->params['controller']));
        }

        return $this->controllerClassName;
    }

    /**
     * @return string
     */
    public function getViewClassName()
    {
        if (null === $this->viewClassName) {
            $this->viewClassName = 'View_'.Inflector::ucWords(Inflector::toUnderscore($this->params['controller'])).'_'.Inflector::toCamelcase($this->params['action'], true);
        }

        return $this->viewClassName;
    }

    /**
     * @return string
     */
    public function getActionMethodName()
    {
        if (null === $this->actionMethodName) {
            $this->actionMethodName = Inflector::toCamelcase($this->params['action']).'Action';
        }

        return $this->actionMethodName;
    }

    /**
     * @return string
     */
    public function getTwigTemplateName()
    {
        if (null === $this->twigTemplateName) {
            $this->twigTemplateName = str_replace(array('_', '.', '-'), '/', $this->params['controller']).'/'.$this->getAction().'.twig';
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
            $value = Arr::get($this->params, $param->getName());
            if (null === $value && $param->isDefaultValueAvailable()) {
                $value = $param->getDefaultValue();
            }
            $params[] = $value;
        }

        return $params;
    }
}
