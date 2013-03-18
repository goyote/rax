<?php

namespace Rax\Mvc;

use ReflectionClass;

class SourceCode
{
    /**
     * @var ReflectionClass
     */
    protected $reflect;

    /**
     * @param string|object $className
     */
    public function __construct($className)
    {
        $this->reflect = new ReflectionClass($className);
    }

    /**
     * Gets the class docblock.
     *
     * @return string
     */
    public function getClassDocblock()
    {
        return $this->cleanDocblock($this->reflect->getDocComment());
    }

    /**
     * Gets the docblock for a method.
     *
     * @param string $methodName
     *
     * @return string
     */
    public function getDocblock($methodName)
    {
        return $this->cleanDocblock($this->reflect->getMethod($methodName)->getDocComment());
    }

    /**
     * Normalizes the whitespace of a docblock.
     *
     * @param string $docblock
     *
     * @return string
     */
    public function cleanDocblock($docblock)
    {
        $lines = array();

        foreach (preg_split('#(\r\n|\n|\r)#', $docblock) as $line) {
            $line = ltrim($line);

            if (preg_match('#^(\*|\*\s+)#', $line)) {
                $line = ' '.$line;
            }

            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    public function getUseStatements()
    {

    }
}
