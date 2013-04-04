<?php

namespace Rax\Mvc\Base;

use Rax\Helper\Arr;
use Rax\Mvc\Debug;
use ReflectionFunction;
use ReflectionMethod;

/**
 *
 */
class BaseDebug
{
    const NO_VALUE = 'NO_VALUE';

    /**
     * @var array
     */
    protected static $topLevelDirs = array(
        'APP_DIR',
        'RAX_DIR',
        'BUNDLES_DIR',
        'CACHE_DIR',
        'LOG_DIR',
        'STORAGE_DIR',
        'WEB_DIR',
        'VENDOR_DIR',
        'ROOT_DIR',
    );

    /**
     * @var array
     */
    protected static $languageConstructs = array(
        'include',
        'include_once',
        'require',
        'require_once',
    );

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

        if (!$return) {
            echo '<pre>'.implode("\n\n", $dump).'</pre>';
            exit();
        }

        return $dump;
    }

    /**
     * @param string $file
     * @param int    $line
     * @param int    $padding
     *
     * @return string
     */
    public static function highlightSourceCode($file, $line, $padding = 7)
    {
        if (!is_readable($file)) {
            return false;
        }

        return htmlspecialchars(file_get_contents($file), ENT_NOQUOTES, 'UTF-8');

        $currentLine = 1;
        $startLine   = ($line > $padding) ? $line - $padding : 1;
        $endLine     = $line + $padding;

        $resource = fopen($file, 'r');

        $sourceCode = '';
        $code = array();
        while (false !== ($row = fgets($resource))) {
            if ($currentLine === $startLine && trim($row) === '') {
                $sourceCode .= "&nbsp;\n";
            } elseif ($currentLine >= $startLine) {
                $sourceCode .= htmlspecialchars($row, ENT_NOQUOTES, 'UTF-8');
                $code[] = rtrim(htmlspecialchars($row, ENT_NOQUOTES, 'UTF-8'));
            }

            if ($currentLine++ >= $endLine) {
                break;
            }
        }

        fclose($resource);



//        $code = array_pad($code, $padding * 2 + 1, "&nbsp;");

        return sprintf(
            '<pre class="brush: %s; first-line: %s; highlight: [%s];">%s</pre>',
            pathinfo($file, PATHINFO_EXTENSION),
            $startLine,
            $line,
            implode("\n", $code)
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
        foreach (static::$topLevelDirs as $dir) {
            /// todo change back

            $absoluteDir = str_replace('/', DS, constant($dir));

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

    /**
     * @param array $stackTrace
     * @param array $options
     *
     * @return array
     */
    public static function trace(array $stackTrace, array $options = array())
    {
        $return = array();

        foreach ($stackTrace as $trace) {
            $temp = Arr::get($trace, array(
                'class',
                'type',
                'function',
                'line',
                'file',
                'call',
                'args',
            ));

            if (
                !$temp['function'] ||
                !is_numeric($temp['line']) ||
                !file_exists($temp['file'])
            ) {
                continue;
            }

            $temp['source'] = Debug::highlightSourceCode($temp['file'], $temp['line'], Arr::get($options, 'sourceCodePadding', 8));


            if ($temp['class']) {
                $temp['call'] = '<span class="class">'.$temp['class'].'</span><span class="type">'.$temp['type'].'</span><span class="function">'.$temp['function'].'</span>';
            } else {
                $temp['call'] = '<span class="function">'.$temp['function'].'</span>';
            }

            if (static::isLanguageConstruct($temp['function'])) {
                $temp['args'] = $temp['args'][0];
            } else {
                if (
                    !method_exists($temp['class'], $temp['function']) &&
                    !function_exists($temp['function']) &&
                    false !== strpos($temp['function'], '{closure}')
                ) {
                    $params = null;
                } else {
                    if ($temp['class']) {
                        if (method_exists($temp['class'], $temp['function'])) {
                            $reflection = new ReflectionMethod($temp['class'], $temp['function']);
                        } else {
                            $reflection = new ReflectionMethod($temp['class'], '__call');
                        }
                    } else {
                        $reflection = new ReflectionFunction($temp['function']);
                    }

                    $params = $reflection->getParameters();

                    $args = array();
                    foreach ($params as $i => $object) {
                        if (isset($temp['args'][$i])) {
                            $args[$object->name] = $temp['args'][$i];
                        } else {
                            $args[$object->name] = static::NO_VALUE;
                        }
                    }

                    $temp['args'] = $args;
                }
            }

            $return[] = $temp;
        }

        return $return;
    }

    /**
     * @param string $function
     *
     * @return bool
     */
    public static function isLanguageConstruct($function)
    {
        return in_array($function, static::$languageConstructs);
    }

//    public static function varName($var) {
//        \Rax\Mvc\Debug::dump(get_defined_vars());
//        $found = false;
//        foreach($GLOBALS as $name => $value) {
//            if ($value === $var) {
//                $found = $name;
//            }
//        }
//
//        return $found;
//    }

    /**
     * @param $var
     */
    public static function dumpMethodArgs(array $var = null)
    {
        echo '<table class="code-dump ace-monokai">';
        foreach ($var as $name => $value) {
            echo '<tr class="depth-1">';
            echo '<td class="arg">$'.$name.'</td>';
            echo '<td>';
            static::__dump($value);
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * @param $var
     */
    public static function __dump($var)
    {
        if (null === $var) {
            echo '<span class="ace_constant ace_language">null</span>';
        } elseif (true === $var) {
            echo '<span class="ace_constant ace_language">true</span>';
        } elseif (false === $var) {
            echo '<span class="ace_constant ace_language">false</span>';
        } elseif (is_string($var)) {
            echo '<span class="ace_string">"'.$var.'"</span>';
        } elseif (is_int($var) || is_float($var)) {
            echo '<span class="ace_constant ace_numeric">'.$var.'</span>';
        } elseif (is_array($var)) {
            if (empty($var)) {
                echo '<span class="ace_constant ace_keyword">array()</span>';
            } else {
                echo '<table class="code-dump ace-monokai">';
                foreach ($var as $name => $value) {
                    echo '<tr>';
                    if (is_string($name)) {
                        echo '<td>'.$name.'</td>';
                    } else {
                        echo '<td><span class="ace_constant ace_numeric">'.$name.'</span></td>';
                    }
                    echo '<td>';
                    static::__dump($value);
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        } elseif (is_object($var)) {
            $class = get_class($var);
            $var = (array) $var;
            if (empty($var)) {
                echo $class;
            } else {
                echo '<div class="header">';
                echo $class;
                echo '</div>';
                echo '<table class="code-dump ace-monokai">';
                foreach ($var as $name => $value) {
                    echo '<tr>';
                    if ($name[0] === "\x00") {
                        $name = '<i class="icon-lock"></i> '.substr($name, strrpos($name, "\x00") + 1);
                    } else {
                        $name = '<i class="icon-unlock"></i> '.$name;
                    }

                    echo '<td class="field">'.$name.'</td>';
                    echo '<td>';
                    static::__dump($value);
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        }
    }

    /**
     * @param mixed    $rawValue
     * @param callable $callback
     * @param int      $length
     *
     * @return array
     */
    public static function value($rawValue, $callback = null, $length = -1)
    {
        $value = $rawValue;

        if (null === $rawValue) {
            $type = $value = 'null';
        } elseif (is_bool($rawValue)) {
            $type = $value = $rawValue ? 'true' : 'false';
        } elseif (is_string($rawValue)) {
            $value = htmlspecialchars($rawValue, ENT_NOQUOTES, 'UTF-8');

//            if ($length > -1 && strlen($value) > $length) {
//                $value = substr($value, 0, $length).'<span class="hellip">&hellip;</span>';
//            };

//            if (false !== strpos($rawValue, '\'')) {
//                if (false !== strpos($rawValue, '"')) {
//                    $value = '\''.str_replace('\'', '\'', $value).'\'';
//                } else {
//                    $value = '"'.$value.'"';
//                }
//            } else {
//            }
            $value = '"'.$value.'"';

            $type = 'string';
        } elseif (is_int($rawValue)) {
            $type = 'int';
        } elseif (is_float($rawValue)) {
            $type = 'float';
        } elseif (is_array($rawValue)) {
            $value = print_r($rawValue, true);
            $type  = 'array';
        } elseif (is_object($rawValue)) {
            $value = print_r($rawValue, true);
            $type  = 'object';
        } elseif (is_callable($rawValue)) {
            $type = 'function';
        } else {
            $type = gettype($rawValue);
        }

        if (null !== $callback && is_callable($callback)) {
            return $callback($value, $type);
        }

        return $value;
    }

    /**
     * @param     $var
     * @param int $length
     * @param int $limit
     * @param int $level
     *
     * @return string
     */
    public static function _dump($var, $length = 128, $limit = 10, $level = 0)
    {
        if (is_resource($var)) {
            if (($type = get_resource_type($var)) === 'stream' AND $meta = stream_get_meta_data($var)) {
                $meta = stream_get_meta_data($var);

                if (isset($meta['uri'])) {
                    $file = $meta['uri'];

                    if (function_exists('stream_is_local')) {
                        // Only exists on PHP >= 5.2.4
                        if (stream_is_local($file)) {
                            $file = Debug::filePath($file);
                        }
                    }

                    return '<small>resource</small><span>('.$type.')</span> '.htmlspecialchars($file, ENT_NOQUOTES, 'UTF-8');
                }
            } else {
                return '<small>resource</small><span>('.$type.')</span>';
            }
        } elseif (is_string($var)) {
            // Clean invalid multibyte characters. iconv is only invoked
            // if there are non ASCII characters in the string, so this
            // isn't too much of a hit.
            //            $var = UTF8::clean($var, Kohana::$charset);

            if (false /*UTF8::strlen($var) > $length*/) {
                // Encode the truncated string
                //                $str = htmlspecialchars(UTF8::substr($var, 0, $length), ENT_NOQUOTES, Kohana::$charset).'&nbsp;&hellip;';
            } else {
                // Encode the string
                $str = htmlspecialchars($var, ENT_NOQUOTES, 'UTF-8');
            }

            if (false !== strpos($str, '"')) {
                if (false !== strpos($str, '"')) {
                    return '"'.str_replace('"', '\"', $str).'"';
                } else {
                    return '"'.str_replace('"', '\"', $str).'"';
                }
            }
        } elseif (is_array($var)) {
            $output = array();

            // Indentation for this variable
            $space = str_repeat($s = '    ', $level);

            static $marker;

            if ($marker === null) {
                // Make a unique marker
                $marker = uniqid("\x00");
            }

            if (empty($var)) {
                // Do nothing
            } elseif (isset($var[$marker])) {
                $output[] = "(\n$space$s*RECURSION*\n$space)";
            } elseif ($level < $limit) {
                $output[] = "<span>(";

                $var[$marker] = true;
                foreach ($var as $key => & $val) {
                    if ($key === $marker) {
                        continue;
                    }
                    if (!is_int($key)) {
                        $key = '"'.htmlspecialchars($key, ENT_NOQUOTES, 'UTF-8').'"';
                    }

                    $output[] = "$space$s$key => ".Debug::_dump($val, $length, $limit, $level + 1);
                }
                unset($var[$marker]);

                $output[] = "$space)</span>";
            } else {
                // Depth too great
                $output[] = "(\n$space$s...\n$space)";
            }

            return '<small>array</small><span>('.count($var).')</span> '.implode("\n", $output);
        } elseif (is_object($var)) {
            // Copy the object as an array
            $array = (array) $var;

            $output = array();

            // Indentation for this variable
            $space = str_repeat($s = '    ', $level);

            $hash = spl_object_hash($var);

            // Objects that are being dumped
            static $objects = array();

            if (empty($var)) {
                // Do nothing
            } elseif (isset($objects[$hash])) {
                $output[] = "{\n$space$s*RECURSION*\n$space}";
            } elseif ($level < $limit) {
                $output[] = "<code>{";

                $objects[$hash] = true;
                foreach ($array as $key => & $val) {
                    if ($key[0] === "\x00") {
                        // Determine if the access is protected or protected
                        $access = '<small>'.(($key[1] === '*') ? 'protected' : 'private').'</small>';

                        // Remove the access level from the variable name
                        $key = substr($key, strrpos($key, "\x00") + 1);
                    } else {
                        $access = '<small>public</small>';
                    }

                    $output[] = "$space$s$access $key => ".Debug::_dump($val, $length, $limit, $level + 1);
                }
                unset($objects[$hash]);

                $output[] = "$space}</code>";
            } else {
                // Depth too great
                $output[] = "{\n$space$s...\n$space}";
            }

            return '<small>object</small> <span>'.get_class($var).'('.count($array).')</span> '.implode("\n", $output);
        } else {
            return '<small>'.gettype($var).'</small> '.htmlspecialchars(print_r($var, true), ENT_NOQUOTES, 'UTF-8');
        }
    }
}
