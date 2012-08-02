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
    public static function loadPhp($file)
    {
        return include $file;
    }
}
