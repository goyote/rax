<?php

/**
 *
 */
class Rax_Text
{
    const PATH_DELIMITER = '.';

    /**
     * @param        $string
     * @param string $delimiter
     *
     * @return string
     */
    public static function ucwords($string, $delimiter = '-')
    {
        return implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
    }
}
