<?php

class Core_Text
{
    const PATH_DELIMITER = '.';

    public static function ucwords($string, $delimiter = '-')
    {
        return implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
    }
}
