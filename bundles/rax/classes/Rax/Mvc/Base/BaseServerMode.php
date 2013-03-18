<?php

namespace Rax\Mvc\Base;

use Exception;
use ReflectionClass;
use InvalidArgumentException;
use Rax\Mvc\ServerMode;

/**
 * The ServerMode class manages the server mode of the currently
 * executing script.
 *
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseServerMode
{
    // Most common server modes
    const PRODUCTION  = 400;
    const STAGING     = 300;
    const TESTING     = 200;
    const DEVELOPMENT = 100;

    /**
     * @var int
     */
    protected $mode;

    /**
     * @param int|string $mode
     */
    public function __construct($mode)
    {
        $this->set($mode);
    }

    /**
     * Sets the server mode.
     *
     * @throws InvalidArgumentException
     *
     * @param int|string $mode
     *
     * @return ServerMode
     */
    public function set($mode)
    {
        if (is_int($mode)) {
            $this->mode = $mode;
        } elseif (is_string($mode)) {
            $this->mode = constant(get_class($this).'::'.strtoupper($mode));
        } else {
            throw new InvalidArgumentException(sprintf('Server mode must be an integer or string, %s given', gettype($mode)));
        }

        return $this;
    }

    /**
     * Returns the server mode as an integer.
     *
     * @return int
     */
    public function get()
    {
        return $this->mode;
    }

    /**
     * Returns the string representation (a.k.a. name) of the server mode.
     *
     * @throws Exception
     * @return string
     */
    public function getName()
    {
        $reflection = new ReflectionClass(get_class($this));

        foreach ($reflection->getConstants() as $name => $value) {
            if ($this->mode === $value) {
                return strtolower($name);
            }
        }

        throw new Exception(sprintf('Current server mode "%s" has no class constant holding its value', $this->mode));
    }

    /**
     * Returns the server mode's short name.
     *
     * @throws Exception
     * @return string
     */
    public function getShortName()
    {
        if ($this->isProd()) {
            return 'prod';
        } elseif ($this->isDev()) {
            return 'dev';
        }

        throw new Exception(sprintf('Current server mode "%s" is not within the dev-prod range', $this->mode));
    }

    /**
     * Checks if the supplied server mode is the current server mode.
     *
     * @param int|string|array $modes
     *
     * @return bool
     */
    public function is($modes)
    {
        foreach ((array) $modes as $mode) {
            if ($mode === $this->get()) {
                return true;
            } elseif (is_string($mode)) {
                $mode = strtolower($mode);
                if ($mode === $this->getName()) {
                    return true;
                } elseif ($mode === $this->getShortName()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Checks if the current server mode is production.
     *
     * @return bool
     */
    public function isProduction()
    {
        return ($this->mode === static::PRODUCTION);
    }

    /**
     * Checks if the current server mode is staging.
     *
     * @return bool
     */
    public function isStaging()
    {
        return ($this->mode === static::STAGING);
    }

    /**
     * Checks if the current server mode is testing.
     *
     * @return bool
     */
    public function isTesting()
    {
        return ($this->mode === static::TESTING);
    }

    /**
     * Checks if the current server mode is development.
     *
     * @return bool
     */
    public function isDevelopment()
    {
        return ($this->mode === static::DEVELOPMENT);
    }

    /**
     * Checks if the current server mode is staging or production.
     *
     * @return bool
     */
    public function isProd()
    {
        return (
            ($this->mode <= static::PRODUCTION) &&
            ($this->mode > static::TESTING)
        );
    }

    /**
     * Checks if the current server mode is testing or development.
     *
     * @return bool
     */
    public function isDev()
    {
        return (
            ($this->mode <= static::TESTING) &&
            ($this->mode >= 0)
        );
    }
}
