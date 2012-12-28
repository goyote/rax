<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
abstract class Rax_FileReader
{
    /**
     * @var string
     */
    protected static $dir;

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
    public static function get($key = null, $default = null, $delimiter = null, $reload = false)
    {
        if (null !== $key) {
            if (null === $delimiter) {
                $delimiter = Text::PATH_DELIMITER;
            }

            $name = current(explode($delimiter, $key));

            if ($reload || !static::isLoaded($name)) {
                static::load($name);
            }
        }

        return Arr::get(static::$storage, $key, $default, $delimiter);
    }

    /**
     * @throws Error
     *
     * @param string $key
     *
     * @return ArrObj
     */
    public static function load($key)
    {
        $exts = array(Environment::getName().'.php', Environment::getShortName().'.php', 'php');
        if (!$files = Autoload::getSingleton()->findFiles(static::$dir, $key, $exts)) {
            throw new Error('Unable to locate a %s file named "%s"', array(strtolower(get_called_class()), $key));
        }

        $files = array_reverse($files);

        $config = array();
        foreach ($files as $file) {
            $config = Arr::merge($config, Php::load($file));
        }

        return (static::$storage[$key] = new ArrObj($config));
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function isLoaded($key)
    {
        return array_key_exists($key, static::$storage);
    }
}
