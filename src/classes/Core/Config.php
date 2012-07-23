<?php

/**
 *
 */
class Core_Config
{
    /**
     * @var array
     */
    protected static $loadedConfigs = array();

    /**
     * @static
     *
     * @param string $key
     * @param mixed  $default
     * @param string $delimiter
     *
     * @return mixed
     */
    public static function get($key, $default = null, $delimiter = '.')
    {
        $configName = array_shift(explode($delimiter, $key));

        if (!static::isLoaded($configName)) {
            static::load($configName);
        }

        return Arr::get(static::$loadedConfigs, $key, $default, $delimiter);
    }

    /**
     * @static
     * @return array
     */
    public static function loadedConfigs()
    {
        return static::$loadedConfigs;
    }

    /**
     * @static
     * @return array
     */
    public static function loadedConfigNames()
    {
        return array_keys(static::$loadedConfigs);
    }

    /**
     * @static
     *
     * @param string $configName
     *
     * @return bool
     */
    public static function isLoaded($configName)
    {
        return array_key_exists($configName, static::$loadedConfigs);
    }

    /**
     * @static
     *
     * @param string $filename
     *
     * @return Config_Group
     */
    public static function load($filename)
    {
        return static::$loadedConfigs[$filename] = new Config_Group($filename, include APP_DIR.'config/'.$filename.'.php');
    }
}
