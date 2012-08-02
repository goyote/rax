<?php

/**
 *
 */
class Core_Config
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
     * @return mixed
     */
    public static function get($key = null, $default = null, $delimiter = Text::PATH_DELIMITER, $reload = false)
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
     * @static
     *
     * @param string $name
     *
     * @throws Exception
     * @return ArrObj
     */
    protected static function load($name)
    {
        $files = Autoload::singleton()->findFiles('config', $name);

        if (empty($files)) {
            throw new Exception(sprintf('Unable to locate a configuration file for %s', $name));
        }

        $files = array_reverse($files);

        $config = array();
        foreach ($files as $file) {
            $config = Arr::merge($config, Filesystem::loadPhp($file));
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
