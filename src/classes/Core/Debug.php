<?php

/**
 *
 */
class Core_Debug
{
    /**
     * Prints information about a variable.
     *
     * This function is meant to replace `print_r()` for debugging purposes
     *
     * @static
     * @param mixed $var
     * @param bool $return
     * @return array
     */
    public static function dump($var, $return = false)
    {
        $dump = array();
        $dump[] = print_r($var, true);

        ob_start();
        var_dump($var);
        $dump[] = ob_get_clean();

        if ($return) {
            return $dump;
        }

        echo '<pre>'.implode('<br />', $dump).'</pre>';
        exit();
    }
}
