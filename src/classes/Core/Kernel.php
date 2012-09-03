<?php

/*
 * This file is part of the Rax framework.
 *
 * (c) Gregorio Ramirez <goyocode@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

/**
 * Kernel class.
 *
 * @package Rax
 * @author  Gregorio Ramirez <goyocode@gmail.com>
 */
class Core_Kernel
{
    const VERSION = '0.1';

    /**
     * @var string
     */
    protected $charset;

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
        if (null === static::$singleton) {
            static::$singleton = new static();
        }

        return static::$singleton;
    }

    /**
     * Constructor.
     *
     * You can define the override the application environment variable in several ways:
     *
     * - Apache: SetEnv APP_ENV development
     * - Nginx: fastcgi_param APP_ENV development
     * - Server variable: export APP_ENV=development
     */
    protected function __construct()
    {

    }

    public function handleRequest()
    {
        echo 'request';
    }

    public function sendResponse()
    {
        echo 'response';
    }

    /**
     * Is debugging turned on?
     *
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }
}
