<?php

use Doctrine\Common\Persistence\Mapping\Driver\DefaultFileLocator;
use Doctrine\Common\Persistence\Mapping\MappingException;

/**
 *
 */
class Rax_RaxFileLocator extends DefaultFileLocator
{
    /**
     * {@inheritdoc}
     */
    public function getAllClassNames($globalBasename)
    {
        $classes = parent::getAllClassNames($globalBasename);

        return Symbol::buildEntityClassNames($classes);
    }

    /**
     * {@inheritdoc}
     */
    public function findMappingFile($className)
    {
        $className = strtolower(substr($className, 7));

        return parent::findMappingFile($className);
    }
}
