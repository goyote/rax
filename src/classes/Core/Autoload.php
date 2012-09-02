<?php

/**
 *
 */
class Core_Autoload
{
    /**
     * @var array
     */
    protected $bundles = array();

    /**
     * @var array
     */
    protected $cascadingFilesystem = array();

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
        if (null === static::$singleton) {
            static::$singleton = new static();
        }

        return static::$singleton;
    }

    /**
     * @return array
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * @param array $bundles
     *
     * @return self
     */
    public function setBundles(array $bundles)
    {
        $dirs = array();
        foreach ($bundles as $name => $dir) {
            $dirs[$name] = $this->normalizeDirPath($dir);
        }

        $this->bundles = $dirs;

        return $this;
    }

    /**
     * @return array
     */
    public function getCascadingFilesystem()
    {
        return $this->cascadingFilesystem;
    }

    /**
     * @param array $cascadingFilesystem
     *
     * @return self
     */
    public function setCascadingFilesystem(array $cascadingFilesystem)
    {
        $dirs = array();
        foreach ($cascadingFilesystem as $dir) {
            if (is_array($dir)) {
                $dirs = array_merge($dirs, array_values($dir));
            } else {
                $dirs[] = $this->normalizeDirPath($dir);
            }
        }

        $this->cascadingFilesystem = $dirs;

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
     * Sets the include path to atuoload PSR-0 compliant classes.
     *
     * @param array|string $includePath
     *
     * @return self
     */
    public function setIncludePath($includePath)
    {
        $includePath = (array) $includePath;

        $dirs = array();
        foreach ($includePath as $dir) {
            $dirs[] = $this->normalizeDirPath($dir);
        }

        $this->includePath = $dirs;

        return $this;
    }

    /**
     * @throws Error
     *
     * @param string $dir
     *
     * @return string
     */
    public function normalizeDirPath($dir)
    {
        if (!is_dir($dir)) {
            throw new Error('%s is not a directory', $dir);
        }

        return realpath($dir).'/';
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
     * Loads a file from in the cascading filesystem.
     *
     * In theory you should never have to call this method directly.
     *
     *     Autoload::getSingleton()->loadClass('FooClass');
     *
     * @param string $class
     *
     * @return self
     */
    public function loadClass($class)
    {
        if ('\\' === $class[0]) {
            $class = substr($class, 1);
        }

        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        $class = str_replace('_', DIRECTORY_SEPARATOR, $class);

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
     * @return bool|string
     */
    public function findFile($baseDir, $file, $ext = 'php')
    {
        $file = $baseDir.DIRECTORY_SEPARATOR.$file.'.'.$ext;

        foreach ($this->cascadingFilesystem as $absoluteDir) {
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
        foreach ($this->cascadingFilesystem as $absoluteDir) {
            if (file_exists($absoluteDir.$file)) {
                $files[] = $absoluteDir.$file;
            }
        }

        return $files;
    }
}
