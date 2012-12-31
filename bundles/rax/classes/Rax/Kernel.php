<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 *
 * @method Kernel  setRouter()
 * @method Kernel  setRequest()
 * @method Request getRequest()
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
     * @var array
     */
    protected $entityManagers = array();

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
    public function getTwigEnvironment()
    {
        if (null === $this->twigEnvironment) {
            $twigLoader      = new Twig_Loader_Filesystem(Autoload::getSingleton()->findDirs('views'));
            $twigEnvironment = new Twig_Environment($twigLoader, Config::get('twig')->asArray());
            $twigEnvironment->addGlobal('request', $this->request);

            $this->twigEnvironment = $twigEnvironment;
        }

        return $this->twigEnvironment;
    }

    /**
     * @param string $connectionName
     * @param bool   $new
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager($connectionName = null, $new = false)
    {
        $connectionName = $connectionName ?: 'default';

        if ($new || !isset($this->entityManagers[$connectionName])) {
            $config = Setup::createConfiguration(Environment::isDev(), Config::get('doctrine.proxyDir'));
            $config->setMetadataDriverImpl(new PhpDriver(Autoload::getSingleton()->findDirs('schema')));

            $this->entityManagers[$connectionName] = EntityManager::create(Config::get('database.'.$connectionName), $config);
        }

        return $this->entityManagers[$connectionName];
    }
}
