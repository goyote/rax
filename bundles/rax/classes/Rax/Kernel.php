<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 *
 * @method Kernel           setRouter()
 * @method Kernel           setRequest()
 * @method Request          getRequest()
 * @method Twig_Environment getTwigEnvironment()
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
     * @var Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * @param Twig_Environment $twigEnvironment
     *
     * @return Kernel
     */
    public function setTwigEnvironment(Twig_Environment $twigEnvironment)
    {
        $twigEnvironment->addGlobal('request', $this->request);
        $this->twigEnvironment = $twigEnvironment;

        return $this;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return 'UTF-8';
    }

    /**
     * @return Response
     */
    public function process()
    {
        if (!$match = $this->router->match($this->request)) {
            // throw 404
        }
        $this->request->setMatchedRoute($match);

        $response = new Response();

        $reflection = new ReflectionClass($match->getControllerClassName());
        $controller = $reflection->newInstance($this->request, $response, $this);

        $reflection->getMethod('before')->invoke($controller);
        $method = $reflection->getMethod($match->getActionMethodName());
        $method->invokeArgs($controller, $match->getMethodArguments($method));
        $reflection->getMethod('after')->invoke($controller);

        return $response;
    }
}
