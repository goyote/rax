<?php

namespace Rax\Mvc\Base;

use Rax\Mvc\Exception;
use InvalidArgumentException;
use ReflectionClass;

/**
 * Environment holds the application environment of the currently executing script.
 *
 * @package   Rax
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseEnvironment
{
    // most common application environments
    const PRODUCTION  = 400;
    const STAGING     = 300;
    const TESTING     = 200;
    const DEVELOPMENT = 100;

    /**
     * The application environment, stored as an integer to allow "greater than"
     * logic in control statements.
     *
     * @var int
     */
    protected static $env;

    /**
     * Sets the application environment.
     *
     * Feel free to use the defined constants or define your own:
     *
     *     Environment::set(Environment::DEVELOPMENT);
     *
     * Note: ideally this value should be set automatically by the framework,
     * provided you have defined an "APP_ENV" environment variable at the
     * server level.
     *
     * @throws InvalidArgumentException
     *
     * @param int $env
     */
    public static function set($env)
    {
        if (is_int($env)) {
            static::$env = $env;
        } elseif (is_string($env)) {
            static::$env = constant(get_called_class().'::'.strtoupper($env));
        } else {
            throw new InvalidArgumentException(sprintf('Parameter $environment must be an integer or string, %s given', gettype($env)));
        }
    }

    /**
     * Returns the application environment as an integer.
     *
     *     Environment::set(Environment::DEVELOPMENT);
     *     Environment::get(); // 100
     *
     * @return int
     */
    public static function get()
    {
        return static::$env;
    }

    /**
     * Returns the string representation (a.k.a name) of the application environment.
     *
     *     Environment::set(Environment::DEVELOPMENT);
     *     Environment::getName(); // "development"
     *
     * @throws Exception
     * @return string
     */
    public static function getName()
    {
        $reflection = new ReflectionClass(get_called_class());

        foreach ($reflection->getConstants() as $name => $value) {
            if (static::$env === $value) {
                return strtolower($name);
            }
        }

        throw new Exception('Current environment "%s" has no class constant holding its value', static::$env);
    }

    /**
     * Returns the application environment's short name.
     *
     *     Environment::set(Environment::DEVELOPMENT); // Or
     *     Environment::set(Environment::TESTING);
     *
     *     Environment::getShortName(); // "dev"
     *
     * @throws Exception
     * @return string
     */
    public static function getShortName()
    {
        if (static::isProd()) {
            return 'prod';
        } elseif (static::isDev()) {
            return 'dev';
        }

        throw new Exception('Current environment "%s" is not within the dev-prod range', static::$env);
    }

    /**
     * Checks if the supplied environment is the current environment.
     *
     *     if (Environment::is('development')) {
     *
     * @param string $env
     *
     * @return bool
     */
    public static function is($env)
    {
        return (static::$env === constant(get_called_class().'::'.strtoupper($env)));
    }

    /**
     * Checks if the current environment is production.
     *
     *     if (Environment::isProduction()) {
     *
     * @return bool
     */
    public static function isProduction()
    {
        return (static::$env === static::PRODUCTION);
    }

    /**
     * Checks if the current environment is staging.
     *
     *     if (Environment::isStaging()) {
     *
     * @return bool
     */
    public static function isStaging()
    {
        return (static::$env === static::STAGING);
    }

    /**
     * Checks if the current environment is testing.
     *
     *     if (Environment::isTesting()) {
     *
     * @return bool
     */
    public static function isTesting()
    {
        return (static::$env === static::TESTING);
    }

    /**
     * Checks if the current environment is development.
     *
     *     if (Environment::isDevelopment()) {
     *
     * @return bool
     */
    public static function isDevelopment()
    {
        return (static::$env === static::DEVELOPMENT);
    }

    /**
     * Checks if the current environment is staging or production.
     *
     *     if (Environment::isProd()) {
     *
     * @return bool
     */
    public static function isProd()
    {
        return (
            (static::$env <= static::PRODUCTION) &&
            (static::$env > static::TESTING)
        );
    }

    /**
     * Checks if the current environment is testing or development.
     *
     *     if (Environment::isDev()) {
     *
     * @return bool
     */
    public static function isDev()
    {
        return (
            (static::$env <= static::TESTING) &&
            (static::$env >= 0)
        );
    }
}
