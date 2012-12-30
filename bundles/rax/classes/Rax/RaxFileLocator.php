<?php

use Doctrine\Common\Persistence\Mapping\Driver\DefaultFileLocator;
use Doctrine\Common\Persistence\Mapping\MappingException;

/**
 *
 */
class Rax_RaxFileLocator extends DefaultFileLocator
{
    /**
     * {@inheritDoc}
     */
    public function getAllClassNames($globalBasename)
    {
        $classes = parent::getAllClassNames($globalBasename);

        return SymbolGenerator::getEntityClassNames($classes);
    }

    /**
     * {@inheritDoc}
     */
    public function findMappingFile($className)
    {
        $className = strtolower(substr($className, 7));

        return parent::findMappingFile($className);
    }
}
