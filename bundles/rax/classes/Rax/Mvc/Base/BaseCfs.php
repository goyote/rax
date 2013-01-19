<?php

namespace Rax\Mvc\Base;

use Closure;
use RuntimeException;
use Rax\Mvc\Cfs;
use Rax\Mvc\Exception;
use Rax\Helper\PhpHelper;

/**
 * The Cfs class maintains the list of loaded bundles.
 *
 * It also provides tools for searching the cascading filesystem for specific
 * set of files and directories paths.
 *
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseCfs
{
    /**
     * @var array
     */
    protected $bundles = array();

    /**
     * @var array
     */
    protected $fileExtension = array('php');

    /**
     * @var Cfs
     */
    protected static $singleton;

    /**
     * Sets the Cfs singleton instance.
     *
     *     $cfs = Cfs::setSingleton(function() {
     *         $cfs = new Cfs();
     *         ...
     *
     *         return $cfs;
     *     });
     *
     * @throws Exception
     *
     * @param Cfs|Closure $singleton
     *
     * @return Cfs
     */
    public static function setSingleton($singleton)
    {
        if ($singleton instanceof Closure) {
            $singleton = $singleton();
        }

        if (!$singleton instanceof static) {
            throw new Exception('Parameter $singleton must be an instanceof %s, %s given',  array(get_called_class(), PhpHelper::getType($singleton)));
        }

        return (static::$singleton = $singleton);
    }

    /**
     * Returns the Cfs singleton instance.
     *
     *     $cfs = Cfs::getSingleton();
     *     $cfs-> ...
     *
     * @return Cfs
     */
    public static function getSingleton()
    {
        return static::$singleton;
    }

    /**
     * Returns a new Cfs instance, useful for chaining.
     *
     *     $cfs = Cfs::create()
     *         -> ...
     *     ;
     *
     * @return Cfs
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Loads the list of bundles found in the configuration.
     *
     *     Cfs::create()->loadBundles(APP_DIR.'config/bundles');
     *
     * @param string $path
     *
     * @return Cfs
     */
    public function loadBundles($path)
    {
        $bundles = array();
        foreach ($this->fileExtension as $ext) {
            if (is_file($file = $path.'.'.$ext)) {
                /** @noinspection PhpIncludeInspection */
                $bundles += require $file;
            }
        }

        $this->setBundles($bundles);

        return $this;
    }

    /**
     * Sets the bundles.
     *
     *     Cfs::create()
     *         ->setBundles(array(
     *             'App' => BUNDLES_DIR.'app',
     *             ...
     *         ));
     *
     * @param array $dirs
     *
     * @return Cfs
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
     * Returns the loaded bundles.
     *
     *     $bundles = Cfs::getSingleton()->getBundles();
     *
     * @return array
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * Normalizes a directory path by appending a trailing slash.
     *
     *     Cfs::normalizeDirPath('/tmp'); // "/tmp/"
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
     * Sets the file extensions.
     *
     *     Cfs::create()
     *         ->setFileExtension(array(
     *             'generated.php',
     *             Environment::getName().'.php',
     *             Environment::getShortName().'.php',
     *             'php',
     *         ));
     *
     * @param array $fileExtension
     *
     * @return Cfs
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = (array) $fileExtension;

        return $this;
    }

    /**
     * Returns the file extensions.
     *
     *     Cfs::getSingleton()->getFileExtension();
     *
     * @return array
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Returns the file path to the first occurrence of the file in the cascading
     * filesystem, false otherwise.
     *
     *     $path = Cfs::getSingleton()->findFile('classes', 'Rax\Mvc\BarClass');
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
            if (is_file($dir.$file)) {
                return $dir.$file;
            }
        }

        return false;
    }

    /**
     * Returns all the file paths found in the cascading filesystem for a given
     * file name.
     *
     *     $files = Cfs::getSingleton()->findFiles('views', 'about/team', 'twig');
     *
     * @param string       $baseDir
     * @param string       $file
     * @param array|string $exts
     *
     * @return array
     */
    public function findFiles($baseDir, $file, $exts = null)
    {
        if (null === $exts) {
            $exts = $this->fileExtension;
        }

        $foundFiles = array();
        foreach ((array) $exts as $ext) {
            /** @noinspection PhpAssignmentInConditionInspection */
            if ($foundFile = $this->findFile($baseDir, $file, $ext)) {
                $foundFiles[] = $foundFile;
            }
        }

        return $foundFiles;
    }

    /**
     * Returns all the directory paths found in the cascading filesystem for a
     * given directory name.
     *
     *     $dirs = Cfs::getSingleton()->findDirs('views');
     *
     * @param array|string $dirs
     *
     * @return array
     */
    public function findDirs($dirs)
    {
        $foundDirs = array();
        foreach ((array) $dirs as $dir) {
            foreach ($this->bundles as $baseDir) {
                if (is_dir($baseDir.$dir)) {
                    $foundDirs[] = $baseDir.$dir;
                }
            }
        }

        return $foundDirs;
    }
}
