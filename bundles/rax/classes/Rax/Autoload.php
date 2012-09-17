<?php

/**
 * Autoload class.
 *
 * @author Gregorio Ramirez <goyocode@gmail.com>
 */
class Rax_Autoload
{
    /**
     * @var array
     */
    protected $bundles = array();

    /**
     * The include path used by the cascading filesystem to autoload a class.
     *
     * @var array
     */
    protected $includePath = array();

    /**
     * Singleton instance.
     *
     * @var self
     */
    protected static $singleton;

    /**
     * Gets a singleton instance.
     *
     * @return self
     */
    public static function getSingleton()
    {
        if (static::$singleton === null) {
            static::$singleton = new static();
        }

        return static::$singleton;
    }

    /**
     * @param array $bundles
     *
     * @return self
     */
    public function setBundles(array $bundles)
    {
        foreach ($bundles as $key => $value) {
            $bundles[$key] = $this->normalizeDirPath($value);
        }

        $this->bundles = $bundles;

        return $this;
    }

    /**
     * @return array
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * Sets the include path to atuoload PSR-0 compliant classes.
     *
     * @param array|string $includePath
     *
     * @return self
     */
    public function setIncludePath($includePath)
    {
        $includePath = (array) $includePath;

        foreach ($includePath as $key => $dir) {
            $includePath[$key] = $this->normalizeDirPath($dir);
        }

        $this->includePath = $includePath;

        return $this;
    }

    /**
     * @return array
     */
    public function getIncludePath()
    {
        return $this->includePath;
    }

    /**
     * @throws RuntimeException
     *
     * @param string $dir
     *
     * @return string
     */
    public function normalizeDirPath($dir)
    {
        if (!is_dir($dir)) {
            throw new RuntimeException(sprintf('%s is not a directory', $dir));
        }

        return realpath($dir).DIRECTORY_SEPARATOR;
    }

    /**
     * @param bool $prepend
     *
     * @return self
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);

        return $this;
    }

    /**
     * @return self
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));

        return $this;
    }

    /**
     * Loads a class file from the cascading filesystem.
     *
     * This method is PSR-0 compliant. The class is loaded on first come first
     * serve basis.
     *
     *     // Load the Foo class from the CFS
     *     Autoload::getSingleton()->loadClass('Foo');
     *
     * @param string $class
     *
     * @return self
     */
    public function loadClass($class)
    {
        if ($class[0] === '\\') {
            $class = substr($class, 1);
        }

        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        $class = str_replace('_',  DIRECTORY_SEPARATOR, $class);

        /** @noinspection PhpAssignmentInConditionInspection */
        if ($file = $this->findFile('classes', $class)) {
            /** @noinspection PhpIncludeInspection */
            require $file;
        } else {
            foreach ($this->includePath as $absoluteDir) {
                if (file_exists($absoluteDir.$class.'.php')) {
                    /** @noinspection PhpIncludeInspection */
                    require $absoluteDir.$class.'.php';

                    break;
                }
            }
        }

        return $this;
    }

    /**
     * @param string $baseDir
     * @param string $file
     * @param string $ext
     *
     * @return string|bool
     */
    public function findFile($baseDir, $file, $ext = 'php')
    {
        $file = $baseDir.DIRECTORY_SEPARATOR.$file.'.'.$ext;

        foreach ($this->bundles as $absoluteDir) {
            if (file_exists($absoluteDir.$file)) {
                return $absoluteDir.$file;
            }
        }

        return false;
    }

    /**
     * @param string $baseDir
     * @param string $file
     * @param string $ext
     *
     * @return array
     */
    public function findFiles($baseDir, $file, $ext = 'php')
    {
        $file = $baseDir.DIRECTORY_SEPARATOR.$file.'.'.$ext;

        $files = array();
        foreach ($this->bundles as $absoluteDir) {
            if (file_exists($absoluteDir.$file)) {
                $files[] = $absoluteDir.$file;
            }
        }

        return $files;
    }
}
