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
     *
     * @param mixed $var
     * @param bool  $return
     *
     * @return array
     */
    public static function dump($var, $return = false)
    {
        $dump   = array();
        $dump[] = print_r($var, true);

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
     * Returns an HTML string, highlighting a specific line of a file with some
     * number of lines padded above and below.
     *
     *     // Highlights the current line of the current file
     *     echo Debug::source(__FILE__, __LINE__);
     *
     * @param   string   file to open
     * @param   integer  line number to highlight
     * @param   integer  number of padding lines
     *
     * @return  string   source of file
     * @return  FALSE    file is unreadable
     */
    public static function source($file, $currentLineNumber, $padding = 5)
    {
        if (!$file OR !is_readable($file)) {
            // Continuing will cause errors
            return false;
        }

        // Open the file and set the line position
        $file                = fopen($file, 'r');
        $lineNumber          = 0;
        $highlightLineNumber = 0;

        // Set the reading range
        $range = array(
            'start' => $currentLineNumber - $padding,
            'end'   => $currentLineNumber + $padding
        );

        $source = '';
        while (($row = fgets($file)) !== false) {
            // Increment the line number
            if (++$lineNumber > $range['end']) {
                break;
            }

            if ($lineNumber >= $range['start']) {
                // Make the row safe for output
                $row = htmlspecialchars($row, ENT_NOQUOTES, 'UTF-8');

                // Trim whitespace and sanitize the row
                $row = $row;

                if ($lineNumber === $currentLineNumber) {
                    $highlightLineNumber = $currentLineNumber;
                    // Apply highlighting to this row
                    $row = $row;
                } else {
                    $row = $row;
                }

                // Add to the captured source
                $source .= $row;
            }
        }

        // Close the file
        fclose($file);

        return "<pre class=\"brush: php; first-line: {$range['start']}; highlight: [$highlightLineNumber];\">\n".$source.'</pre>';
    }

    public static function filePath($file)
    {
        if (strpos($file, APP_DIR) === 0) {
            $file = 'APP_DIR'.DIRECTORY_SEPARATOR.substr($file, strlen(APP_DIR));
        } elseif (strpos($file, BUNDLES_DIR) === 0) {
            $file = 'BUNDLES_DIR'.DIRECTORY_SEPARATOR.substr($file, strlen(BUNDLES_DIR));
        } elseif (strpos($file, SRC_DIR) === 0) {
            $file = 'SRC_DIR'.DIRECTORY_SEPARATOR.substr($file, strlen(SRC_DIR));
        } elseif (strpos($file, VENDOR_DIR) === 0) {
            $file = 'VENDOR_DIR'.DIRECTORY_SEPARATOR.substr($file, strlen(VENDOR_DIR));
        } elseif (strpos($file, WEB_DIR) === 0) {
            $file = 'WEB_DIR'.DIRECTORY_SEPARATOR.substr($file, strlen(WEB_DIR));
        } elseif (strpos($file, ROOT_DIR) === 0) {
            $file = 'ROOT_DIR'.DIRECTORY_SEPARATOR.substr($file, strlen(ROOT_DIR));
        }

        return $file;
    }
}
