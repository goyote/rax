<?php

/**
 *
 */
class Rax_Php
{
    /**
     * @static
     *
     * @param string $file
     *
     * @return mixed
     */
    public static function load($file)
    {
        /** @noinspection PhpIncludeInspection */
        return include $file;
    }

    /**
     * @static
     *
     * @param callable|mixed $value
     *
     * @return mixed
     */
    public static function value($value)
    {
        return ($value instanceof Closure) ? $value() : $value;
    }
}
