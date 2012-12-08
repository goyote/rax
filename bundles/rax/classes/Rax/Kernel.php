<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Kernel
{
    /**
     * Rax version.
     *
     * @see http://semver.org/
     */
    const VERSION = '0.1.0';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Singleton instance.
     *
     * @var self
     */
    protected static $singleton;

    /**
     * Gets a singleton instance.
     *
     * @return self
     */
    public static function getSingleton()
    {
        if (static::$singleton === null) {
            static::$singleton = new static();
        }

        return static::$singleton;
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        $this->router;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function process(Request $request)
    {
        $params = $this->router->match($request);

        $reflection = $this->createReflectionController($params);

        $request
            ->setController($params['controller'])
            ->setAction($params['action'])
        ;

        $response = new Response();

        $controller = $reflection->newInstance($request, $response);

        $reflection->getMethod('before')->invoke($controller);
        $reflection->getMethod($this->createActionName($params))->invoke($controller);
        $reflection->getMethod('after')->invoke($controller);

        return $response;
    }

    /**
     * @param array $params
     *
     * @return ReflectionClass
     */
    public function createReflectionController(array $params)
    {
        $controller = $params['controller'];
        $controller = str_replace(array('-', '.'), '_', $controller);
        // todo check existence of route controller key
        // todo check existence of controller

        return new ReflectionClass('Controller_'.$controller);
    }

    public function createActionName(array $params)
    {
        $action = $params['action'];
        return $action.'Action';
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return 'UTF-8';
    }
}
