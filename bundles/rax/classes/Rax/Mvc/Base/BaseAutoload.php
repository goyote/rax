<?php

namespace Rax\Mvc\Base;

use Closure;
use Exception;
use Rax\Helper\Php;
use Rax\Mvc\Autoload;
use Rax\Mvc\Cfs;

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
    protected $classMap = array();

    /**
     * @var array
     */
    protected $namespaces = array();

    /**
     * @param Cfs $cfs
     */
    public function __construct(Cfs $cfs)
    {
        $this->cfs = $cfs;
    }

    /**
     * Sets the class map.
     *
     * @param array $classMap
     *
     * @return $this
     */
    public function setClassMap(array $classMap)
    {
        $this->classMap = $classMap;

        return $this;
    }

    /**
     * Returns the class map.
     *
     * @return array
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    /**
     * Sets the namespaces.
     *
     * @param array $namespaces
     *
     * @return Autoload
     */
    public function setNamespaces(array $namespaces)
    {
        $this->namespaces = $namespaces;

        return $this;
    }

    /**
     * Returns the namespaces.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
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
     */
    public function loadClass($class)
    {
        if ($file = $this->findFile($class)) {
            require $file;
        }
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

        if ($file = $this->cfs->findFile('classes', $classPath)) {
            return $file;
        }

        $classPath .= '.php';

        foreach ($this->namespaces as $namespace => $dir) {
            if (0 === strpos($class, $namespace)) {
                if (is_file($dir.$classPath)) {
                    return $dir.$classPath;
                }
            }
        }

        return false;
    }
}
