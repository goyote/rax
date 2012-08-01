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
     * @return bool
     */
    public static function isLoaded($name)
    {
        return array_key_exists($name, static::$storage);
    }

    /**
     * @static
     *
     * @param string $name
     *
     * @return ArrObj
     */
    public static function load($name)
    {
        $config = array();

        if ($files = Find::singleton()->findFile('config', $name)) {
            foreach ($files as $file) {
                $config = Arr::merge($config, Filesystem::load($file));
            }
        }

        return static::$storage[$name] = new ArrObj($config, ArrayObject::ARRAY_AS_PROPS);
    }
}
