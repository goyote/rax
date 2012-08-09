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
     * @param string   $file
     * @param callable $callback
     *
     * @return string
     */
    public static function filePath($file, $callback = null)
    {
        $dirs = array(
            'APP_DIR',
            'BUNDLES_DIR',
            'SRC_DIR',
            'VENDOR_DIR',
            'WEB_DIR',
            'ROOT_DIR',
        );

        foreach ($dirs as $dir) {
            $absoluteDir = constant($dir);

            if (0 === strpos($file, $absoluteDir)) {
                $file = DIRECTORY_SEPARATOR.substr($file, strlen($absoluteDir));

                if (null !== $callback && is_callable($callback)) {
                    return $callback($dir, $file);
                }

                $file = $dir.$file;
                break;
            }
        }

        return $file;
    }
}
