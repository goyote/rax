<?php

use Doctrine\ORM\Tools\EntityRepositoryGenerator;

/**
 *
 */
class Rax_RaxEntityRepositoryGenerator extends EntityRepositoryGenerator
{
    protected static $_template =
'<?php

/**
 * <name> repository class.
 *
 * NOTE: This class was auto generated for your convenience. Add your own custom
 * repository methods below.
 *
 * To add global methods that affect all repositories, use the Repository_Base
 * class. Inheritance order:
 *
 * <className> > Repository_Base > EntityRepository
 *
 * @package Rax\Repository
 */
class <className> extends Repository_Base
{
}
';

    public function generate(array $metadatas, $destPath)
    {
        foreach ($metadatas as $metadata) {
            if ($metadata->customRepositoryClassName) {
                $this->writeEntityRepositoryClass($metadata->customRepositoryClassName, $destPath);
            }
        }
    }

    public function generateEntityRepositoryClass($fullClassName)
    {
        $className = $fullClassName;
//        $className = substr($fullClassName, strrpos($fullClassName, '\\') + 1, strlen($fullClassName));

        $variables = array(
            '<name>' => ucfirst(\Inflector::toHuman(substr($className, 11))),
            '<className>' => $className,
        );

        return str_replace(array_keys($variables), array_values($variables), self::$_template);
    }

    public function writeEntityRepositoryClass($fullClassName, $outputDirectory)
    {
        $code = $this->generateEntityRepositoryClass($fullClassName);

        $path = $outputDirectory . DIRECTORY_SEPARATOR
                . str_replace('\\', \DIRECTORY_SEPARATOR, substr($fullClassName, 11)) . '.php';
        $dir = dirname($path);

        if ( ! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // todo readd
        // make entity repository generator equal to entity generator
//        if ( ! file_exists($path)) {
            file_put_contents($path, $code);
//        }
    }
}
