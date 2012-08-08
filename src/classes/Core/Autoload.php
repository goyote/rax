<?php

/**

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
    public static function singleton()
    {
        if (null === static::$singleton) {
            static::$singleton = new static();
        }

        return static::$singleton;
    }

    /**
     * @param array $bundles
     *
     * @return array|self
     */
    public function bundles(array $bundles = null)
    {
        if (null === $bundles) {
            return $this->bundles;
        }

        $dirs = array();
        foreach ($bundles as $name => $dir) {
            $dirs[$name] = $this->normalizeDirPath($dir);
        }

        $this->bundles = $dirs;

        return $this;
    }

    /**
     * @param array $cascadingFilesystem
     *
     * @return self
     */
    public function cascadingFilesystem(array $cascadingFilesystem)
    {
        if (null === $cascadingFilesystem) {
            return $this->cascadingFilesystem;
        }

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
     * Sets the include path from which PSR-0 compliant classes will be loaded.
     *
     * @param string|array $includePath
     *
     * @return self
     */
    public function includePath($includePath)
    {
        if (null === $includePath) {
            return $this->includePath;
        }

        $dirs        = array();
        $includePath = (array) $includePath;

        foreach ($includePath as $dir) {
            $dirs[] = $this->normalizeDirPath($dir);
        }

        $this->includePath = $dirs;

        return $this;
    }

    /**
     * @param string $dir
     *
     * @throws Error
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
            require $file;
        }

        foreach ($this->includePath as $absoluteDir) {
            if (file_exists($absoluteDir.$class.'.php')) {
                require $absoluteDir.$class.'.php';

                break;
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
