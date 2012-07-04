<?php

class Core_Config
{
    /**
     * @var string
     */
    protected static $delimiter = '.';

    /**
     * @var array
     */
    protected static $storage = array();

    /**
     * @static
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $filename = static::getFilenameFromPath($key);

        if (!static::isLoaded($filename)) {
            static::load($filename);
        }

        return Arr::getFromPath(static::$storage, $key, $default);
    }

    /**
     * @static
     * @param string $key
     * @return bool
     */
    public static function isLoaded($key)
    {
        return array_key_exists($key, static::$storage);
    }

    /**
     * @static
     * @param string $key
     * @param string $delimiter
     * @return string
     */
    public static function getFilenameFromPath($key, $delimiter = null)
    {
        if (is_null($delimiter)) {
            $delimiter = static::getDelimiter();
        }

        if (false === strpos($key, $delimiter)) {
            return $key;
        }

        return strstr($key, $delimiter, true);
    }

    /**
     * @static
     * @param string$filename
     * @return ArrayObject
     */
    public static function load($filename)
    {
        return static::$storage[$filename] = new ArrayObject(include APP_DIR.'config/'.$filename.'.php', ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @static
     * @return string
     */
    public static function getDelimiter()
    {
        return self::$delimiter;
    }

    /**
     * @static
     * @param $delimiter
     */
    public static function setDelimiter($delimiter)
    {
        self::$delimiter = $delimiter;
    }
}
