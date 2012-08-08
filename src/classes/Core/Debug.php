<?php

/**
 *
 */
class Core_Debug
{
    /**
     * Prints information about a variable.
     *
     * This function is meant to replace `print_r()` for debugging purposes.
     *
     * @static
     *
     * @param mixed $var
     * @param bool  $return
     *
     * @return array
     */
    public static function dump($var, $return = false)
    {
        $dump = array();

        if ('' !== $var && null !== $var && !is_bool($var)) {
            $dump[] = print_r($var, true);
        }

        ob_start();
        var_dump($var);
        $dump[] = ob_get_clean();

        if ($return) {
            return $dump;
        }

        echo '<pre>'.implode("\n\n", $dump).'</pre>';
        exit();
    }

    /**
     * @param string $file
     * @param int    $line
     * @param int    $padding
     *
     * @return string
     */
    public static function sourceCode($file, $line, $padding = 5)
    {
        if (!is_readable($file)) {
            return false;
        }

        $resource        = fopen($file, 'r');
        $currentLine     = 1;
        $highlightedLine = 1;

        $startLine = ($line > $padding) ? $line - $padding : 1;
        $endLine   = $line + $padding;

        $sourceCode = '';
        while (false !== ($row = fgets($resource))) {
            if ($currentLine >= $startLine) {
                $sourceCode .= htmlspecialchars($row, ENT_NOQUOTES, 'UTF-8');
            }
            if ($currentLine++ >= $endLine) {
                break;
            }
        }

        fclose($resource);

        return sprintf(
            '<pre class="brush: %s; first-line: %s; highlight: [%s];">%s</pre>',
            pathinfo($file, PATHINFO_EXTENSION),
            $startLine,
            $line,
            $sourceCode
        );
    }

    /**
     * @param string $file
     * @param bool   $html
     *
     * @return string
     */
    public static function filePath($file, $html = false)
    {
        if (0 === strpos($file, APP_DIR)) {
            $dir = 'APP_DIR';
        } elseif (0 === strpos($file, BUNDLES_DIR)) {
            $dir = 'BUNDLES_DIR';
        } elseif (0 === strpos($file, SRC_DIR)) {
            $dir = 'SRC_DIR';
        } elseif (0 === strpos($file, VENDOR_DIR)) {
            $dir = 'VENDOR_DIR';
        } elseif (0 === strpos($file, WEB_DIR)) {
            $dir = 'WEB_DIR';
        } elseif (0 === strpos($file, ROOT_DIR)) {
            $dir = 'ROOT_DIR';
        }

        if (isset($dir)) {
            $file = DIRECTORY_SEPARATOR.substr($file, strlen(constant($dir)));

            if ($html) {
                $dir = '<span class="dir-const">'.$dir.'</span>';
            }

            return $dir.$file;
        }

        return $file;
    }
}
