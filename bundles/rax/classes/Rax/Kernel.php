<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Kernel extends Object
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
     * Returns a singleton instance.
     *
     * @return self
     */
    public static function getSingleton()
    {
        if (null === static::$singleton) {
            static::$singleton = new static();
        }

        return static::$singleton;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function process(Request $request)
    {
        if (!$match = $this->router->match($request)) {
            // throw 404
        }
        $request->setMatchedRoute($match);

        $response = new Response();

        $reflection = new ReflectionClass($match->getControllerClass());
        $controller = $reflection->newInstance($request, $response);

        $reflection->getMethod('before')->invoke($controller);
        $method = $reflection->getMethod($match->getActionMethod());
        $method->invokeArgs($controller, $match->getMethodArguments($method));
        $reflection->getMethod('after')->invoke($controller);

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
