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
     * Returns the application environment as either an integer or string.
     *
     *     Environment::set(Environment::DEVELOPMENT);
     *
     *     $environment = Environment::get();     // 100
     *     $environment = Environment::get(true); // "development"
     *
     * @param bool $string
     *
     * @return int|string
     */
    public static function get($string = false)
    {
        if ($string) {
            $reflection = new ReflectionClass(get_called_class());

            foreach ($reflection->getConstants() as $name => $value) {
                if (static::$environment === $value) {
                    return strtolower($name);
                }
            }
        }

        return static::$environment;
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
        return (static::$environment === constant(get_called_class().'::'.strtoupper($environment)));
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
            static::$environment <= static::PRODUCTION &&
            static::$environment > static::TESTING
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
            static::$environment <= static::TESTING &&
            static::$environment >= 0
        );
    }
}
