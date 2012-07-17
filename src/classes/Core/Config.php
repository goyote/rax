<?php

/**
 *
 */
class Core_Config
{
    /**
     * @var string
     */
    protected static $delimiter = '.';

    /**
     * @var array
     */
    protected static $loadedFiles = array();

    /**
     * @static
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $filename = current(explode(static::$delimiter, $key));

        if (!static::isLoaded($filename)) {
            static::load($filename);
        }

        return Arr::path(static::$loadedFiles, $key, $default);
    }

    /**
     * @static
     * @param string $key
     * @return bool
     */
    public static function isLoaded($key)
    {
        return array_key_exists($key, static::$loadedFiles);
    }

    /**
     * @static
     * @param string$filename
     * @return ArrayObject
     */
    public static function load($filename)
    {
        return static::$loadedFiles[$filename] = new ArrayObject(include APP_DIR.'config/'.$filename.'.php', ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @static
     *
     * @param null $delimiter
     *
     * @return string
     */
    public static function delimiter($delimiter = null)
    {
        if (null === $delimiter) {
            return static::$delimiter;
        }

        return static::$delimiter = $delimiter;
    }
}
