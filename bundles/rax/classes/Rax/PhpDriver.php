<?php

use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\Common\Persistence\Mapping\MappingException;

/**
 *
 */
class Rax_PhpDriver extends YamlDriver
{
    /**
     * @var string
     */
    const DEFAULT_FILE_EXTENSION = '.php';

    /**
     * {@inheritDoc}
     */
    public function __construct($locator, $fileExtension = PhpDriver::DEFAULT_FILE_EXTENSION)
    {
        parent::__construct(new RaxFileLocator((array) $locator, $fileExtension), $fileExtension);
    }

    /**
     * {@inheritDoc}
     */
    protected function loadMappingFile($file)
    {
        return Php::load($file);
    }

    /**
     * {@inheritDoc}
     */
    public function getElement($className)
    {
        if ($this->classCache === null) {
            $this->initialize();
        }

        if (isset($this->classCache[$className])) {
            return $this->classCache[$className];
        }

        $result = $this->loadMappingFile($this->locator->findMappingFile($className));
        if (!is_array($result)) {
            throw MappingException::invalidMappingFile($className, str_replace('\\', '.', $className) . $this->locator->getFileExtension());
        }

        if (!isset($result['type'])) {
            $result['type'] = 'entity';
        }

        if (!isset($result['table'])) {
            $result['table'] = substr(strtolower($className), 7);
        }

        return $result;
    }
}
