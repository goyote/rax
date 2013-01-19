<?php

namespace Rax\Data\Base;

use Rax\Helper\ArrHelper;
use Rax\Helper\PhpHelper;
use Rax\Helper\TextHelper;
use Rax\Mvc\ArrObj;
use Rax\Mvc\Cfs;
use Rax\Mvc\Environment;
use Rax\Mvc\Exception;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012-2013 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
abstract class BaseFileReader
{
    /**
     * @var string
     */
    protected static $dataDir;

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
                $delimiter = TextHelper::PATH_DELIMITER;
            }

            $name = current(explode($delimiter, $key));

            if ($reload || !static::isLoaded($name)) {
                static::load($name);
            }
        }

        return ArrHelper::get(static::$storage, $key, $default, $delimiter);
    }

    /**
     * @throws Exception
     *
     * @param string $key
     *
     * @return ArrObj
     */
    public static function load($key)
    {
        $exts = array(Environment::getName().'.php', Environment::getShortName().'.php', 'php');
        if (!$files = Cfs::getSingleton()->findFiles(static::$dataDir, $key, $exts)) {
            throw new Exception('Unable to locate a %s file named %s.php', array(strtolower(get_called_class()), $key));
        }

        $files = array_reverse($files);

        $config = array();
        foreach ($files as $file) {
            $config = ArrHelper::merge($config, PhpHelper::load($file));
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
