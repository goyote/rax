<?php

/**
 * Autoload is responsible for autoloading PHP classes, taking out the burden of
 * calling include() each time you want to use a class located on a different file.
 *
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class Rax_Autoload
{
    /**
     * @var array
     */
    protected $bundles = array();

    /**
     * @var array
     */
    protected $includePath = array();

    /**
     * @var Autoload
     */
    protected static $singleton;

    /**
     * Returns an Autoload singleton instance.
     *
     *     $autoload = Autoload::getSingleton();
     *
     * @return Autoload
     */
    public static function getSingleton()
    {
        if (null === static::$singleton) {
            static::$singleton = new static();
        }

        return static::$singleton;
    }

    /**
     * Sets the bundles.
     *
     *     Autoload::getSingleton()
     *         ->setBundles(array(
     *             'App' => BUNDLES_DIR.'app',
     *             ...
     *         ));
     *
     * @param array $dirs
     *
     * @return Autoload
     */
    public function setBundles(array $dirs)
    {
        foreach ($dirs as $name => $path) {
            $dirs[$name] = static::normalizeDirPath($path);
        }
        $this->bundles = $dirs;

        return $this;
    }

    /**
     * Returns the bundles.
     *
     *     $bundles = Autoload::getSingleton()->getBundles();
     *
     * @return array
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * Sets the include path.
     *
     * If a class cannot be found in the CFS, the autoloader will look into this
     * array of dirs for a match. The file is loaded on a first-come first-serve
     * basis. This lookup is PSR-0 compliant :)
     *
     *     Autoload::getSingleton()->setIncludePath(VENDOR_DIR);
     *
     * @param array|string $dirs
     *
     * @return Autoload
     */
    public function setIncludePath($dirs)
    {
        $dirs = (array) $dirs;
        foreach ($dirs as $key => $path) {
            $dirs[$key] = static::normalizeDirPath($path);
        }
        $this->includePath = $dirs;

        return $this;
    }

    /**
     * Returns the include path.
     *
     *     $includePath = Autoload::getSingleton()->getIncludePath();
     *
     * @return array
     */
    public function getIncludePath()
    {
        return $this->includePath;
    }

    /**
     * Makes sure the directory path always ends with a slash.
     *
     *     // Returns "/tmp/"
     *     $dir = Autoload::normalizeDirPath('/tmp');
     *
     * @throws RuntimeException
     *
     * @param string $dir
     *
     * @return string
     */
    public static function normalizeDirPath($dir)
    {
        if (is_dir($dir)) {
            return realpath($dir).'/';
        }

        throw new RuntimeException(sprintf('%s is not a directory', $dir));
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
     * @return Autoload
     */
    public function loadClass($class)
    {
        if ('\\' === $class[0]) {
            $class = substr($class, 1);
        }
        $class = str_replace(array('\\', '_'), '/', $class);

        /** @noinspection PhpAssignmentInConditionInspection */
        if ($file = $this->findFile('classes', $class)) {
            /** @noinspection PhpIncludeInspection */
            require $file;
        } else {
            foreach ($this->includePath as $dir) {
                if (file_exists($dir.$class.'.php')) {
                    /** @noinspection PhpIncludeInspection */
                    require $dir.$class.'.php';

                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Returns the full file path to the first occurrence of the file in
     * the CFS, false otherwise.
     *
     *      $filePath = Autoload::getSingleton()->findFile('classes', 'BarClass');
     *
     * @param string $baseDir
     * @param string $file
     * @param string $ext
     *
     * @return string|bool
     */
    public function findFile($baseDir, $file, $ext = 'php')
    {
        $file = $baseDir.'/'.$file.'.'.$ext;
        foreach ($this->bundles as $dir) {
            if (file_exists($dir.$file)) {
                return $dir.$file;
            }
        }

        return false;
    }

    /**
     * Returns all the file paths found in the CFS for a given partial file name.
     *
     *     $filePaths = Autoload::getSingleton()->findFiles('classes', 'BarClass');
     *
     * @param string $baseDir
     * @param string $file
     * @param string $ext
     *
     * @return array
     */
    public function findFiles($baseDir, $file, $ext = 'php')
    {
        $file = $baseDir.'/'.$file.'.'.$ext;
        $files = array();
        foreach ($this->bundles as $dir) {
            if (file_exists($dir.$file)) {
                $files[] = $dir.$file;
            }
        }

        return $files;
    }
}
