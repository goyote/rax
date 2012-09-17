<?php

/**
 *
 */
class Rax_Config
{
    /**
     * @var array
     */
    protected static $storage = array();

    /**
     * @static
     *
     * @param string $key
     * @param mixed  $default
     * @param string $delimiter
     * @param bool   $reload
     *
     * @return ArrObj|mixed
     */
    public static function get($key = null, $default = null, $delimiter = null, $reload = false)
    {
        if ($key === null) {
            return static::$storage;
        }

        $name = current(explode($delimiter, $key));

        if ($reload || !static::isLoaded($name)) {
            static::load($name);
        }

        return Arr::get(static::$storage, $key, $default, $delimiter);
    }

    /**
     * @static
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

        return static::$storage[$name] = new ArrObj($config, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @static
     *
     * @param string $name
     *
     * @return bool
     */
    public static function isLoaded($name)
    {
        return array_key_exists($name, static::$storage);
    }
}
