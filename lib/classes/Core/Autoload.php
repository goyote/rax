<?php

class Core_Autoload
{
    /**
     * The include path used by the cascading filesystem to autoload a class.
     *
     * @var array
     */
    protected $includePath = array();

    /**
     * @var array
     */
    protected $bundles = array();

    /**
     * @var array
     */
    protected $cascadingFilesystem = array();

    /**
     * Singleton instance.
     *
     * @var Autoload
     */
    protected static $instance;

    /**
     * Gets a singleton instance.
     *
     * @return Autoload
     */
    public static function getSingleton()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param array $bundles
     * @return Autoload
     */
    public function setBundles(array $bundles)
    {
        foreach ($bundles as $name => $path) {
            $bundles[$name] = rtrim($path, '/').'/';
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
     * Sets the include path to be used by the cascading filesystem.
     *
     * @param string|array $includePath
     * @return Autoload
     */
    public function setIncludePath($includePath)
    {
        $this->includePath = (array) $includePath;

        return $this;
    }

    /**
     * Gets the cascading filesystem include path.
     *
     * @return array
     */
    public function getIncludePath()
    {
        return $this->includePath;
    }

    /**
     * @param array $directories
     * @return Autoload
     */
    public function setCascadingFilesystem(array $directories)
    {
        $cascadingFilesystem = array();

        foreach ($directories as $directory) {
            if (is_array($directory)) {
                $cascadingFilesystem = array_merge($cascadingFilesystem, $directory);
            } else {
                $cascadingFilesystem[] = $directory;
            }
        }

        $this->cascadingFilesystem = $cascadingFilesystem;

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
     * @param bool $prepend
     * @return Autoload
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);

        return $this;
    }

    /**
     * Loads a file found in the cascading filesystem.
     *
     * In theory you should never have to call this method directly.
     *
     *     Autoload::getSingleton()->loadClass('FooClass');
     *
     * @param string $class
     */
    public function loadClass($class)
    {
        if ($file = $this->findFile('classes', $class)) {
            require $file;
        }
    }

    /**
     * Returns the absolute file path of a class.
     *
     * @param string $baseDir
     * @param string $class
     * @param string $ext
     * @return bool|string
     */
    public function findFile($baseDir = '', $class, $ext = 'php')
    {
        if ('\\' === $class[0]) {
            $class = substr($class, 1);
        }

        if (false !== strpos($class, '\\')) {
            $class = str_replace('\\', '/', $class);
        }

        $class = '/'.str_replace('_', '/', $class).'.'.$ext;

        foreach ($this->cascadingFilesystem as $absoluteDir) {
            if (file_exists($absoluteDir.$baseDir.$class)) {
                return $absoluteDir.$baseDir.$class;
            }
        }

        foreach ($this->includePath as $absoluteDir) {
            if (file_exists($absoluteDir.$class)) {
                return $absoluteDir.$class;
            }
        }

        return false;
    }
}
