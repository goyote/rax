<?php

/*
 * This file is part of the Rax PHP framework.
 *
 * (c) Gregorio Ramirez <goyocode@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

/**
 * Environment class.
 *
 * The application environment can be defined at the server level:
 *
 * - Apache: SetEnv APP_ENV development
 * - Nginx:  fastcgi_param APP_ENV development
 * - Shell:  export APP_ENV=development
 *
 * @package Rax
 * @author  Gregorio Ramirez <goyocode@gmail.com>
 */
class Rax_Environment
{
    // Common application environments
    const PRODUCTION  = 400;
    const STAGING     = 300;
    const TESTING     = 200;
    const DEVELOPMENT = 100;

    /**
     * Application environment.
     *
     * We store it as an integer to allow "greater than" logic in control
     * statements.
     *
     * @var int
     */
    protected static $environment;

    /**
     * Sets the application environment.
     *
     * Feel free to use the defined constants or create your own:
     *
     *     Environment::set(Environment::DEVELOPMENT);
     *
     * This value will be set automatically by the framework, provided you have
     * defined the "APP_ENV" environment variable at the server level.
     *
     * @static
     * @throws InvalidArgumentException
     *
     * @param int $environment
     */
    public static function set($environment)
    {
        if (is_int($environment)) {
            static::$environment = $environment;
        } elseif (is_string($environment)) {
            static::$environment = constant('Environment::'.strtoupper($environment));
        } else {
            throw new InvalidArgumentException(sprintf(
                '%s() expects parameter 1 to be an integer or string, %s given',
                __METHOD__,
                gettype($environment)
            ));
        }
    }

    /**
     * Gets the application environment.
     *
     * @static
     *
     * @return int
     */
    public static function get()
    {
        return static::$environment;
    }

    /**
     * Checks if the current environment is production.
     *
     *     if (Environment::isProduction()) {
     *
     * @static
     *
     * @return bool
     */
    public static function isProduction()
    {
        return (static::$environment === static::PRODUCTION);
    }

    /**
     * Checks if the current environment is staging.
     *
     *     if (Environment::isStaging()) {
     *
     * @static
     *
     * @return bool
     */
    public static function isStaging()
    {
        return (static::$environment === static::STAGING);
    }

    /**
     * Checks if the current environment is testing.
     *
     *     if (Environment::isTesting()) {
     *
     * @static
     *
     * @return bool
     */
    public static function isTesting()
    {
        return (static::$environment === static::TESTING);
    }

    /**
     * Checks if the current environment is development.
     *
     *     if (Environment::isDevelopment()) {
     *
     * @static
     *
     * @return bool
     */
    public static function isDevelopment()
    {
        return (static::$environment === static::DEVELOPMENT);
    }

    /**
     * Checks if the current environment is staging or production.
     *
     *     if (Environment::isProd()) {
     *
     * @static
     *
     * @return bool
     */
    public static function isProd()
    {
        return (
            static::$environment <= static::PRODUCTION &&
            static::$environment > static::TESTING
        );
    }

    /**
     * Checks if the current environment is testing or development.
     *
     *     if (Environment::isDev()) {
     *
     * @static
     *
     * @return bool
     */
    public static function isDev()
    {
        return (
            static::$environment <= static::TESTING &&
            static::$environment >= 0
        );
    }
}
