<?php

/**
 * Environment specifies the application/server environment in which current
 * script is running.
 *
 * Use this class to execute special logic only when the script is ran in a
 * specific environment, e.g. turn on debugging and profiling in development
 * mode.
 *
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Environment
{
    // Most common application environments
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
            throw new InvalidArgumentException(
                sprintf('Environment::set() expects parameter 1 to be an integer or string, %s given', gettype($environment))
            );
        }
    }

    /**
     * Returns the application environment as an integer.
     *
     *     Environment::set(Environment::DEVELOPMENT);
     *
     *     $environment = Environment::get(); // 100
     *
     * @return int
     */
    public static function get()
    {
        return static::$environment;
    }

    /**
     * Returns the application environment's string representation (a.k.a name.)
     *
     *     Environment::set(Environment::DEVELOPMENT);
     *
     *     $environment = Environment::getName(); // "development"
     *
     * @throws Error
     * @return string
     */
    public static function getName()
    {
        $reflection = new ReflectionClass('Environment');

        foreach ($reflection->getConstants() as $name => $value) {
            if (static::$environment === $value) {
                return strtolower($name);
            }
        }

        throw new Error('Current environment "%s" has no class constant holding its value', static::$environment);
    }

    /**
     * Returns the application environment's short name.
     *
     *     Environment::set(Environment::DEVELOPMENT); // Or
     *     Environment::set(Environment::TESTING);
     *
     *     $environment = Environment::getShortName(); // "dev"
     *
     * @throws Error
     * @return string
     */
    public static function getShortName()
    {
        if (static::isProd()) {
            return 'prod';
        } elseif (static::isDev()) {
            return 'dev';
        }

        throw new Error('Current environment "%s" is not within the dev-prod range', static::$environment);
    }

    /**
     * Checks if the supplied environment is the current environment.
     *
     *     if (Environment::is('development')) {
     *
     * @param string $environment
     *
     * @return bool
     */
    public static function is($environment)
    {
        return (static::$environment === constant('Environment::'.strtoupper($environment)));
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
        return (static::$environment === static::PRODUCTION);
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
        return (static::$environment === static::STAGING);
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
        return (static::$environment === static::TESTING);
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
        return (static::$environment === static::DEVELOPMENT);
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
            (static::$environment <= static::PRODUCTION) &&
            (static::$environment > static::TESTING)
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
            (static::$environment <= static::TESTING) &&
            (static::$environment >= 0)
        );
    }
}
