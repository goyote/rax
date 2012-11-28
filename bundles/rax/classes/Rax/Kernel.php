<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Kernel
{
    const VERSION = '0.1';

    /**
     * @var string
     */
    protected $charset;

    /**
     * @var Request
     */
    protected $request;

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

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    public function getRouter()
    {
        $this->router;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function setConfig(ArrObj $config)
    {
    }

    /**
     *
     */
    public function processRequest()
    {
        $match = $this->router->match($this->request->getUri());
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return 'utf-8';
    }
}
