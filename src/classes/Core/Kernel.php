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
     * Debug mode.
     *
     * @var boolean
     */
    protected $debug;

    /**
     * @var string
     */
    protected $charset;

    /**
     * Singleton instance.
     *
     * @var Kernel
     */
    protected static $instance;

    /**
     * Gets a singleton instance.
     *
     * @return Kernel
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
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
        // TODO use Config::get('Database');
        $config    = Yaml::parse(APP_DIR.'Config/App.yml');
        $appConfig = $config['app'];


        if (null === $appConfig['debug']) {
            $this->debug = $this->isTestingOrDevelopment();
        } else {
            $this->debug = (boolean) $appConfig['debug'];
        }

        if ($this->isDebug()) {
            error_reporting(E_ALL | E_STRICT);
            ini_set('display_errors', 1);
        } else {
            error_reporting(E_ALL & ~E_DEPRECATED);
            ini_set('display_errors', 0);
        }

        date_default_timezone_set($appConfig['timezone']);
        setlocale(LC_ALL, $appConfig['locale']);

        $this->setCharset(strtolower($appConfig['charset']));

        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding($this->getCharset());
        }
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
