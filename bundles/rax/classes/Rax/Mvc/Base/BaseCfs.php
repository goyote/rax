<?php

namespace Rax\Mvc\Base;

use Closure;
use Exception;
use RuntimeException;
use Rax\Mvc\Cfs;
use Rax\Helper\Php;
use Symfony\Component\Finder\Finder;
use Rax\Mvc\ServerMode;

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
     * @var ServerMode
     */
    protected $serverMode;

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
     * @param ServerMode $serverMode
     */
    public function __construct(ServerMode $serverMode)
    {
        $this->serverMode = $serverMode;
    }

    /**
     * Sets the bundles.
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
     * Returns the bundles.
     *
     * @return array
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * Returns the file extensions.
     *
     * @return array
     */
    public function getFileExtensions()
    {
        return array(
            'generated.php',
            $this->serverMode->getName().'.php',
            $this->serverMode->getShortName().'.php',
            'php',
        );
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
        foreach ($this->getFileExtensions() as $ext) {
            if (is_file($file = $path.'.'.$ext)) {
                $bundles += require $file;
            }
        }

        $this->setBundles($bundles);

        return $this;
    }

    /**
     * Bootstrap each bundle.
     *
     * @return Cfs
     */
    public function bootstrap()
    {
        foreach (array_reverse($this->bundles) as $bundle) {
            if (is_file($file = $bundle.'bootstrap.php')) {
                require $file;
            }
        }

        return $this;
    }

    /**
     * Returns the file path to the first occurrence of the file in the cascading
     * filesystem, false otherwise.
     *
     *     $path = Cfs::getSingleton()->findFile('classes', 'Rax\Mvc\BarClass');
     *
     * @param string $scanDir
     * @param string $file
     * @param string $ext
     *
     * @return string|bool
     */
    public function findFile($scanDir, $file, $ext = 'php')
    {
        $file = $scanDir.'/'.$file.'.'.$ext;

        foreach ($this->bundles as $bundle) {
            if (is_file($bundle.$file)) {
                return $bundle.$file;
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
     * @param string       $dir
     * @param string       $file
     * @param string|array $exts
     *
     * @return array
     */
    public function findFiles($dir, $file, $exts = null)
    {
        $file = $dir.'/'.$file;

        if (null === $exts) {
            $exts = $this->getFileExtensions();
        }

        $foundFiles = array();
        foreach ((array) $exts as $ext) {
            foreach ($this->bundles as $bundle) {
                if (is_file($bundle.$file.'.'.$ext)) {
                    $foundFiles[] = $bundle.$file.'.'.$ext;
                }
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
            foreach ($this->bundles as $bundleDir) {
                if (is_dir($bundleDir.$dir)) {
                    $foundDirs[] = $bundleDir.$dir;
                }
            }
        }

        return $foundDirs;
    }
}
