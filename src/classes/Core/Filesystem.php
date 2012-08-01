<?php

/**
 *
 */
class Core_Filesystem
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
}
