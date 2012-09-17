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
class Rax_Kernel
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
        if (static::$singleton === null) {
            static::$singleton = new static();
        }

        return static::$singleton;
    }

    /**
     * Constructor.
     */
    protected function __construct()
    {

    }

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Request $request
     *
     * @return self
     */
    public function handleRequest(Request $request)
    {
        $this->request = $request;

        return $this;
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
     * @return string
     */
    public function getCharset()
    {
        return 'UTF-8';
    }
}
