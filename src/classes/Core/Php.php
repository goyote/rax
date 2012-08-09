<?php

class Core_Php
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
        return include $file;
    }

    /**
     * @static
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function value($value)
    {
        return ($value instanceof Closure) ? $value() : $value;
    }
}
