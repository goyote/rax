<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\DBAL\Types\Type;
use Doctrine\Common\Util\Inflector;

/**
 *
 */
class Rax_RaxEntityGenerator extends EntityGenerator
{
    /**
     * Hash-map for handle types
     *
     * @var array
     */
    protected $typeAlias = array(
        Type::DATETIMETZ   => '\DateTime',
        Type::DATETIME     => '\DateTime',
        Type::DATE         => '\DateTime',
        Type::TIME         => '\DateTime',
        Type::OBJECT       => '\stdClass',
        Type::SMALLINT     => 'int',
        Type::INTEGER      => 'int',
        Type::BIGINT       => 'int',
        Type::BOOLEAN      => 'bool',
        Type::TEXT         => 'string',
        Type::BLOB         => 'string',
        Type::DECIMAL      => 'float',
        Type::JSON_ARRAY   => 'array',
        Type::SIMPLE_ARRAY => 'array',
    );

    /**
     * @var string
     */
    protected static $classTemplate =
'<?php

/**
 * <entity> entity class.
 *
 * NOTE: This file was created so you can safely override any aspect of the
 * auto generated base entity.
 *
 * To apply logic to all entities, use the Entity_Base class. Inheritance order:
 *
 * <entityName> > <entityBaseName> > Entity_Base
 *
 * @package Rax\Entity
 */
<entityClassName>
{
}
';

    /**
     * @var string
     */
    protected static $baseClassTemplate =
'<?php

/**
 * WARNING: Auto generated base entity class.
 *
 * Please don\'t edit this class directly. Any changes made to this file will
 * be overridden on schema update. Your customizations should instead go in the
 * <entityName> class or Entity_Base for global logic.
 *
 * @package Rax\Entity\Base
 */
<entityClassName>
{
<entityBody>
}
';

    /**
     * @var string
     */
    protected static $setMethodTemplate =
'/**
 * <description>.
 *
 * @param <variableType>$<variableName>
 *
 * @return <entity>
 */
public function <methodName>(<methodTypeHint>$<variableName><variableDefault>)
{
<spaces>$this-><fieldName> = $<variableName>;

<spaces>return $this;
}';

    /**
     * @var string
     */
    protected static $getMethodTemplate =
'/**
 * <description>.
 *
 * @return <variableType>
 */
public function <methodName>()
{
<spaces>return $this-><fieldName>;
}';

    /**
     * @var string
     */
    protected static $addMethodTemplate =
'/**
 * <description>.
 *
 * @param <variableType>$<variableName>
 *
 * @return <entity>
 */
public function <methodName>(<methodTypeHint>$<variableName>)
{
<spaces>$this-><fieldName>[] = $<variableName>;

<spaces>return $this;
}';

    /**
     * @var string
     */
    protected static $removeMethodTemplate =
'/**
 * <description>.
 *
 * @param <variableType>$<variableName>
 */
public function <methodName>(<methodTypeHint>$<variableName>)
{
<spaces>$this-><fieldName>->removeElement($<variableName>);
}';

    /**
     * @var string
     */
    protected static $lifecycleCallbackMethodTemplate =
'/**
 * @<name>
 */
public function <methodName>()
{
<spaces>// Add your code here
}';

    /**
     * @var string
     */
    protected static $constructorMethodTemplate =
'/**
 * Constructor.
 */
public function __construct()
{
<spaces><collections>
}
';

    /**
     * {@inheritDoc}
     */
    public function generate(array $metadatas, $outputDirectory)
    {
        foreach ($metadatas as $metadata) {
            $this->writeEntityClass($metadata, $outputDirectory);
            $this->writeBaseEntityClass($metadata, $outputDirectory);
        }
    }

    public function writeEntityClass(ClassMetadataInfo $metadata, $outputDirectory)
    {
        $path = $outputDirectory . '/' . str_replace('\\', DIRECTORY_SEPARATOR, substr($metadata->name, 7)) . $this->extension;

        if (file_exists($path)) {
            return;
        }

        $dir = dirname($path);

        if ( ! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->isNew = !file_exists($path) || (file_exists($path) && $this->regenerateEntityIfExists);

        if ( ! $this->isNew) {
            $this->parseTokensInEntityFile(file_get_contents($path));
        } else {
            $this->staticReflection[$metadata->name] = array('properties' => array(), 'methods' => array());
        }

        if ($this->backupExisting && file_exists($path)) {
            $backupPath = dirname($path) . DIRECTORY_SEPARATOR . basename($path) . "~";
            if (!copy($path, $backupPath)) {
                throw new \RuntimeException("Attempt to backup overwritten entity file but copy operation failed.");
            }
        }

        // If entity doesn't exist or we're re-generating the entities entirely
        if ($this->isNew) {
            file_put_contents($path, $this->generateEntityClass($metadata));
            // If entity exists and we're allowed to update the entity class
        } elseif ( ! $this->isNew && $this->updateEntityIfExists) {
            file_put_contents($path, $this->generateUpdatedEntityClass($metadata, $path));
        }
    }

    public function writeBaseEntityClass(ClassMetadataInfo $metadata, $outputDirectory)
    {
        $path = $outputDirectory . '/Base/' . str_replace('\\', DIRECTORY_SEPARATOR, substr($metadata->name, 7)) . $this->extension;
        $dir = dirname($path);

        if ( ! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->isNew = !file_exists($path) || (file_exists($path) && $this->regenerateEntityIfExists);

        if ( ! $this->isNew) {
            $this->parseTokensInEntityFile(file_get_contents($path));
        } else {
            $this->staticReflection[$metadata->name] = array('properties' => array(), 'methods' => array());
        }

        if ($this->backupExisting && file_exists($path)) {
            $backupPath = dirname($path) . DIRECTORY_SEPARATOR . basename($path) . "~";
            if (!copy($path, $backupPath)) {
                throw new \RuntimeException("Attempt to backup overwritten entity file but copy operation failed.");
            }
        }

        // If entity doesn't exist or we're re-generating the entities entirely
        if ($this->isNew) {
            file_put_contents($path, $this->generateBaseEntityClass($metadata));
            // If entity exists and we're allowed to update the entity class
        } elseif ( ! $this->isNew && $this->updateEntityIfExists) {
            file_put_contents($path, $this->generateUpdatedEntityClass($metadata, $path));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function generateEntityClass(ClassMetadataInfo $metadata)
    {
        $placeHolders = array(
            '<entity>',
            '<entityName>',
            '<entityBaseName>',
            '<entityClassName>',
        );

        $replacements = array(
            ucfirst(\Inflector::toHuman(substr($metadata->name, 7))),
            $metadata->name,
            'Entity_Base_'.substr($metadata->name, 7),
            $this->generateEntityClassName($metadata),
        );

        $code = str_replace($placeHolders, $replacements, static::$classTemplate);

        return str_replace('<spaces>', $this->spaces, $code);
    }

    public function generateBaseEntityClass(ClassMetadataInfo $metadata)
    {
        $placeHolders = array(
            '<namespace>',
            '<entityName>',
            '<entityClassName>',
            '<entityBody>'
        );

        $replacements = array(
            $this->generateEntityNamespace($metadata),
            $metadata->name,
            $this->generateBaseEntityClassName($metadata),
            $this->generateEntityBody($metadata)
        );

        $code = str_replace($placeHolders, $replacements, static::$baseClassTemplate);

        return str_replace('<spaces>', $this->spaces, $code);
    }

    protected function generateEntityBody(ClassMetadataInfo $metadata)
    {
        $fieldMappingProperties = $this->generateEntityFieldMappingProperties($metadata);
        $associationMappingProperties = $this->generateEntityAssociationMappingProperties($metadata);
        $stubMethods = $this->generateEntityStubMethods ? $this->generateEntityStubMethods($metadata) : null;
        $lifecycleCallbackMethods = $this->generateEntityLifecycleCallbackMethods($metadata);

        $code = array();

        if ($fieldMappingProperties) {
            $code[] = $fieldMappingProperties;
        }

        if ($associationMappingProperties) {
            $code[] = $associationMappingProperties;
        }

        if ($constructor = $this->generateEntityConstructor($metadata)) {
            $code[] = $constructor;
        }

        if ($stubMethods) {
            $code[] = $stubMethods;
        }

        if ($lifecycleCallbackMethods) {
            $code[] = $lifecycleCallbackMethods;
        }

        return implode("\n", $code);
    }

    protected function generateEntityFieldMappingProperties(ClassMetadataInfo $metadata)
    {
        $lines = array();

        foreach ($metadata->fieldMappings as $fieldMapping) {
            if ($this->hasProperty($fieldMapping['fieldName'], $metadata) ||
                $metadata->isInheritedField($fieldMapping['fieldName'])) {
                continue;
            }

            $lines[] = $this->generateFieldMappingPropertyDocBlock($fieldMapping, $metadata);
            $lines[] = $this->spaces . $this->fieldVisibility . ' $' . $fieldMapping['fieldName']
                       . (isset($fieldMapping['default']) ? ' = ' . var_export($fieldMapping['default'], true) : null) . ";\n";
        }

        return implode("\n", $lines);
    }

    protected function generateEntityStubMethod(ClassMetadataInfo $metadata, $type, $fieldName, $typeHint = null,  $defaultValue = null)
    {
        $methodName = $type . Inflector::classify($fieldName);
        if (in_array($type, array("add", "remove")) && substr($methodName, -1) == "s") {
            $methodName = substr($methodName, 0, -1);
        }

        if ($this->hasMethod($methodName, $metadata)) {
            return '';
        }
        $this->staticReflection[$metadata->name]['methods'][] = $methodName;

        $var = sprintf('%sMethodTemplate', $type);
        $template = static::$$var;

        $methodTypeHint = null;
        $types          = Type::getTypesMap();
        $variableType   = $typeHint ? $this->getType($typeHint) . ' ' : null;

        if ($typeHint && ! isset($types[$typeHint])) {
            $variableType   =  '\\' . ltrim($variableType, '\\');
            $methodTypeHint =  '\\' . $typeHint . ' ';
        }

        $replacements = array(
            '<description>'       => ucfirst($type) . 's the ' . $fieldName,
            '<methodTypeHint>'    => $methodTypeHint,
            '<variableType>'      => $variableType,
            '<variableName>'      => Inflector::camelize($fieldName),
            '<methodName>'        => $methodName,
            '<fieldName>'         => $fieldName,
            '<variableDefault>'   => ($defaultValue !== null ) ? (' = '.$defaultValue) : '',
            '<entity>'            => $this->getClassName($metadata)
        );

        $method = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );

        return $this->prefixCodeWithSpaces($method);
    }

    /**
     * @param Doctrine\ORM\Mapping\ClassMetadataInfo $metadata
     *
     * @return string
     */
    protected function generateEntityClassName(ClassMetadataInfo $metadata)
    {
        return 'class ' . $this->getClassName($metadata) . ' extends Entity_Base_' .substr($this->getClassName($metadata), 7);
    }

    /**
     * @param Doctrine\ORM\Mapping\ClassMetadataInfo $metadata
     *
     * @return string
     */
    protected function generateBaseEntityClassName(ClassMetadataInfo $metadata)
    {
        return 'class ' . $this->getBaseClassName($metadata) .
               ($this->extendsClass() ? ' extends ' . $this->getClassToExtendName() : null);
    }

    /**
     * @param Doctrine\ORM\Mapping\ClassMetadataInfo $metadata
     *
     * @return string
     */
    protected function getBaseClassName(ClassMetadataInfo $metadata)
    {
        return 'Entity_Base_'.substr($metadata->name, 7);
    }

    /**
     * @return string
     */
    protected function getClassToExtendName()
    {
        $reflection = new ReflectionClass($this->getClassToExtend());

        return $reflection->getName();
    }
}
