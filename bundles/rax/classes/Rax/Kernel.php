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
     * @var Twig_Environment
     */
    protected $twig;

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
        $this->request = $request;

        if (!$match = $this->router->match($request)) {
            // throw 404
        }
        $request->setMatchedRoute($match);

        $response = new Response();

        $reflection = new ReflectionClass($match->getControllerClassName());
        $controller = $reflection->newInstance($request, $response, $this);

        $reflection->getMethod('before')->invoke($controller);
        $method = $reflection->getMethod($match->getActionMethodName());
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

    /**
     * @return Twig_Environment
     */
    public function getTwig()
    {
        if (null === $this->twig) {
            $loader = new Twig_Loader_Filesystem(Autoload::getSingleton()->findDirs('views'));
            $twig = new Twig_Environment($loader, array(
                'cache'               => CACHE_DIR.'twig',
                'charset'             => $this->getCharset(),
                'debug'               => Environment::isDev(),
                'auto_reload'         => Environment::isDev(),
                'strict_variables'    => Environment::isDev(),
                'base_template_class' => 'Twig_Template',
                'autoescape'          => 'html',
                'optimizations'       => -1,
            ));
            $twig->addGlobal('request', $this->request);
            $this->twig = $twig;
        }

        return $this->twig;
    }
}
