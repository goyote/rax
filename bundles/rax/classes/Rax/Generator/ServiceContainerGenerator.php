<?php

namespace Rax\Generator;

use Closure;
use Exception;
use Rax\Mvc\Cfs;
use Rax\Mvc\ServiceContainer;

class ServiceContainerGenerator
{
    /**
     * @var string
     */
    protected $saveFile;

    /**
     * @var Cfs
     */
    protected $cfs;

    /**
     * @var ServiceContainer
     */
    protected $service;

    /**
     * @param Cfs     $cfs
     * @param ServiceContainer $service
     */
    public function __construct(Cfs $cfs, ServiceContainer $service)
    {
        $this->cfs     = $cfs;
        $this->service = $service;
    }

    /**
     * Sets the output file path.
     *
     * @param string $saveFile
     *
     * @return $this
     */
    public function setSaveFile($saveFile)
    {
        $this->saveFile = $saveFile;

        return $this;
    }

    /**
     * Returns the output file path.
     *
     * @return string
     */
    public function getSaveFile()
    {
        return $this->saveFile;
    }

    /**
     * Generates the content and outputs the result to file.
     */
    public function generate()
    {
        if (!is_dir(dirname($this->saveFile))) {
            mkdir(dirname($this->saveFile), 0777, true);
        }

//        FileSystem::save();
        // file_put_contents(FileSystem$this->saveFile, $this->getTemplate());
        file_put_contents($this->saveFile, $this->getContent());
    }

    /**
     * Builds the content.
     *
     * @return string
     */
    public function getContent()
    {
        return strtr($this->getTemplate(), $this->getVars());
    }

    /**
     * Returns the template source code.
     *
     * @return string
     */
    public function getTemplate()
    {
        return file_get_contents($this->cfs->findFile('views', 'rax/generator/service-generator/class', 'tmpl'));
    }

    /**
     * Gets the replacement variables.
     *
     * @return array
     */
    public function getVars()
    {
        $classes = $this->service->listClasses();

        return array(
            '<useClasses>'   => $this->getClasses($classes),
            '<propertyTags>' => $this->getProperties($classes),
            '<methodTags>'   => $this->getMethods($classes),
        );
    }

    /**
     * @param array $classes
     *
     * @return string
     */
    public function getClasses(array $classes)
    {
        $classes[] = 'Rax\Mvc\Base\BaseServiceContainer';
        asort($classes);

        $tmp = array();
        foreach ($classes as $class) {
            $tmp[] = str_replace('<class>', $class, 'use <class>;');
        }

        return implode("\n", $tmp);
    }

    /**
     * Gets a list of docblock property tags.
     *
     * @param array $classes
     *
     * @return string
     *
     * @link http://phpdoc.org/docs/latest/for-users/phpdoc/tags/property.html
     */
    public function getProperties(array $classes)
    {
        ksort($classes);

        $maxlen = 0;
        $tmp = array();
        foreach ($classes as $id => $class) {
            if (false !== strpos($class, '\\')) {
                $class = substr($class, strrpos($class, '\\') + 1);
            }

            if (($strlen = strlen($class)) > $maxlen) {
                $maxlen = $strlen;
            }

            $tmp[] = array(
                '<class>'  => $class,
                '<var>'    => $id,
            );
        }

        foreach ($tmp as $i => $item) {
            $tmp[$i] = strtr(' * @property <class> <spaces>$<var>', $item + array(
                '<spaces>' => str_pad('', $maxlen - strlen($item['<class>'])),
            ));
        }

        return implode("\n", $tmp);
    }

    /**
     * Gets a list of docblock method tags.
     *
     * @param array $classes
     *
     * @return string
     *
     * @link http://phpdoc.org/docs/latest/for-users/phpdoc/tags/property.html
     */
    public function getMethods(array $classes)
    {
        ksort($classes);

        $maxlen = 0;
        $tmp = array();
        foreach ($classes as $id => $class) {
            if (false !== strpos($class, '\\')) {
                $class = substr($class, strrpos($class, '\\') + 1);
            }

            if (($strlen = strlen($class)) > $maxlen) {
                $maxlen = $strlen;
            }

            $tmp[] = array(
                '<class>'  => $class,
                '<method>' => ucfirst($id),
            );
        }

        foreach ($tmp as $i => $item) {
            $tmp[$i] = strtr(' * @method <class> <spaces>get<method>()', $item + array(
                '<spaces>' => str_pad('', $maxlen - strlen($item['<class>'])),
            ));
        }

        return implode("\n", $tmp);
    }
}
