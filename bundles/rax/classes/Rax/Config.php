<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Config
{
    /**
     * @var array
     */
    protected static $storage = array();

    /**
     * @param string $key
     * @param mixed  $default
     * @param string $delimiter
     * @param bool   $reload
     *
     * @return ArrObj|mixed
     */
    public static function get($key = null, $default = null, $delimiter = '.', $reload = false)
    {
        if (null === $key) {
            return static::$storage;
        }

        $name = current(explode($delimiter, $key));

        if ($reload || !static::isLoaded($name)) {
            static::load($name);
        }

        return Arr::get(static::$storage, $key, $default, $delimiter);
    }

    /**
     * @throws RuntimeException
     *
     * @param string $name
     *
     * @return ArrObj
     */
    public static function load($name)
    {
        if (!$files = Autoload::getSingleton()->findFiles('config', $name)) {
            throw new RuntimeException(sprintf('Unable to locate configuration files for %s', $name));
        }

        $files = array_reverse($files);

        $config = array();
        foreach ($files as $file) {
            $config = Arr::merge($config, Php::load($file));
        }

        return static::$storage[$name] = new ArrObj($config);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public static function isLoaded($name)
    {
        return array_key_exists($name, static::$storage);
    }
}
