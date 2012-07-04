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
 * Environment class.
 *
 * The application environment can be defined in several ways:
 *
 * - Apache: SetEnv APP_ENV development
 * - Nginx:  fastcgi_param APP_ENV development
 * - Shell:  export APP_ENV=development
 *
 * @package Rax
 * @author  Gregorio Ramirez <goyocode@gmail.com>
 */
class Core_Environment
{
    // List of the most common environments
    const PRODUCTION  = 400;
    const STAGING     = 300;
    const TESTING     = 200;
    const DEVELOPMENT = 100;

    /**
     * The application environment.
     *
     * We store it as an integer to allow "greater than" logic in control
     * statements.
     *
     * @var integer
     */
    protected static $environment;

    /**
     * Set the application environment.
     *
     * Feel free to use the defined constants or create your own:
     *
     *     Environment::set(Environment::DEVELOPMENT);
     *
     * This value will be set automatically by the framework, provided you have
     * defined the "APP_ENV" environment variable in one of the many ways
     * outlined in the class DocBlock.
     *
     * @static
     * @param integer $environment
     */
    public static function set($environment)
    {
        static::$environment = (integer) $environment;
    }

    /**
     * Get the current application environment.
     *
     * @static
     * @return integer
     */
    public static function get()
    {
        return static::$environment;
    }

    /**
     * Are we in a production environment?
     *
     * @static
     * @return boolean
     */
    public static function isProduction()
    {
        return (static::PRODUCTION === static::get());
    }

    /**
     * Are we in a staging environment?
     *
     * @static
     * @return boolean
     */
    public static function isStaging()
    {
        return (static::STAGING === static::get());
    }

    /**
     * Are we in a testing environment?
     *
     * @static
     * @return boolean
     */
    public static function isTesting()
    {
        return (static::TESTING === static::get());
    }

    /**
     * Are we in a development environment?
     *
     * @static
     * @return boolean
     */
    public static function isDevelopment()
    {
        return (static::DEVELOPMENT === static::get());
    }

    /**
     * Are we in a staging/production environment?
     *
     * @static
     * @return boolean
     */
    public static function isProd()
    {
        return (static::STAGING <= static::get());
    }

    /**
     * Are we in a testing/development environment?
     *
     * @static
     * @return boolean
     */
    public static function isDev()
    {
        return (static::TESTING >= static::get());
    }
}
