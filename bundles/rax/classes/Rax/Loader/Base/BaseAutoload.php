<?php

namespace Rax\Loader\Base;

use Closure;
use Rax\Helper\PhpHelper;
use Rax\Loader\Autoload;
use Rax\Mvc\Cfs;
use Rax\Mvc\Exception;

/**
 * The Autoloader class is responsible for the autoloading of PHP classes.
 *
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012-2013 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseAutoload
{
    /**
     * @var Cfs
     */
    protected $cfs;

    /**
     * @var array
     */
    protected $namespaces = array();

    /**
     * @var array
     */
    protected $classMap = array();

    /**
     * @var Autoload
     */
    protected static $singleton;

    /**
     * Sets the Autoload singleton instance.
     *
     *     $autoload = Autoload::setSingleton(function() {
     *         $autoload = new Autoload();
     *         ...
     *
     *         return $autoload;
     *     });
     *
     * @throws Exception
     *
     * @param Autoload|Closure $singleton
     *
     * @return Autoload
     */
    public static function setSingleton($singleton)
    {
        if ($singleton instanceof Closure) {
            $singleton = $singleton();
        }

        if (!$singleton instanceof static) {
            throw new Exception('Parameter $singleton must be an instanceof %s, %s given', array(get_called_class(), PhpHelper::getType($singleton)));
        }

        return (static::$singleton = $singleton);
    }

    /**
     * Returns the Autoload singleton instance.
     *
     *     $autoload = Autoload::getSingleton();
     *     $autoload-> ...
     *
     * @return Autoload
     */
    public static function getSingleton()
    {
        return static::$singleton;
    }

    /**
     * Returns a new Autoload instance, useful for chaining.
     *
     *     $autoload = Autoload::create()
     *         -> ...
     *     ;
     *
     * @return Autoload
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Sets the Cfs instance.
     *
     *     Autoload::getSingleton()->setCfs(Cfs::getSingleton());
     *
     * @param Cfs $cfs
     *
     * @return Autoload
     */
    public function setCfs(Cfs $cfs)
    {
        $this->cfs = $cfs;

        return $this;
    }

    /**
     * Returns the Cfs instance.
     *
     *     $cfs = Autoload::getSingleton()->getCfs();
     *
     * @return Autoload
     */
    public function getCfs()
    {
        return $this->cfs;
    }

    /**
     * Sets the namespace mappings.
     *
     *     Autoload::getSingleton()->setNamespaces(array(
     *         'Vendor\\Namespace' => '/path/to/src/',
     *     ));
     *
     * @param array $namespaces
     *
     * @return Autoload
     */
    public function setNamespaces(array $namespaces)
    {
        foreach ($namespaces as $namespace => $dir) {
            $this->addNamespace($namespace, $dir);
        }

        return $this;
    }

    /**
     * Returns the namespace mappings.
     *
     *     $namespaces = Autoload::getSingleton()->getNamespaces();
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * @param string $namespace
     * @param string $dir
     *
     * @return Autoload
     */
    public function addNamespace($namespace, $dir)
    {
        $dir = array_map(array($this->cfs, 'normalizeDirPath'), (array) $dir);

        if (isset($this->namespaces[$namespace])) {
            $this->namespaces[$namespace] = array_merge($this->namespaces[$namespace], $dir);
        } else {
            $this->namespaces[$namespace] = $dir;
        }

        return $this;
    }

    /**
     * Sets the class to file mappings.
     *
     *     Autoload::getSingleton()->setClassMap(array(
     *         'Vendor\\Namespace\\Class' => '/path/to/Vendor/Namespace/Class.php',
     *     ));
     *
     * @param array $classMap
     *
     * @return $this
     */
    public function setClassMap(array $classMap)
    {
        $this->classMap += $classMap;

        return $this;
    }

    /**
     * Returns the class to file mappings.
     *
     *     $classMap = Autoload::getSingleton()->getClassMap();
     *
     * @return array
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    /**
     * Registers the class autoloader.
     *
     *     Autoload::getSingleton()->register();
     *
     * @param bool $prepend
     *
     * @return Autoload
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);

        return $this;
    }

    /**
     * Unregisters the class autoloader.
     *
     *     Autoload::getSingleton()->unregister();
     *
     * @return Autoload
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));

        return $this;
    }

    /**
     * Loads a class file from the cascading filesystem (CFS.)
     *
     * This method is PSR-0 compliant. The class is loaded on first-come
     * first-serve basis.
     *
     *     Autoload::getSingleton()->loadClass('BarClass');
     *
     * @param string $class
     *
     * @return bool
     */
    public function loadClass($class)
    {
        /** @noinspection PhpAssignmentInConditionInspection */
        if ($file = $this->findFile($class)) {
            /** @noinspection PhpIncludeInspection */
            require $file;

            return true;
        }

        return false;
    }

    /**
     * @param string $class
     *
     * @return string|bool
     */
    public function findFile($class)
    {
        if ('\\' === $class[0]) {
            $class = substr($class, 1);
        }

        if (isset($this->classMap[$class])) {
            return $this->classMap[$class];
        }

        $classPath = str_replace(array('\\', '_'), '/', $class);

        /** @noinspection PhpAssignmentInConditionInspection */
        if ($file = $this->cfs->findFile('classes', $classPath)) {
            return $file;
        }

        $classPath .= '.php';

        foreach ($this->namespaces as $namespace => $dirs) {
            if (0 === strpos($class, $namespace)) {
                foreach ($dirs as $dir) {
                    if (is_file($dir.$classPath)) {
                        return $dir.$classPath;
                    }
                }
            }
        }

        return false;
    }
}
