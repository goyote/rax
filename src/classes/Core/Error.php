<?php

/**

 */
class Core_Error extends Exception
{
    /**
     * @var array
     */
    public static $levels = array(
        E_ERROR             => 'Fatal Error',
        E_STRICT            => 'Runtime Notice',
        E_PARSE             => 'Parse Error',
        E_WARNING           => 'Warning',
        E_NOTICE            => 'Notice',
        E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        E_DEPRECATED        => 'Deprecated',
        E_USER_ERROR        => 'User Error',
        E_USER_NOTICE       => 'User Notice',
        E_USER_WARNING      => 'User Warning',
        E_USER_DEPRECATED   => 'User Deprecated',
    );

    /**
     * @param string       $message
     * @param string|array $values
     * @param int          $code
     * @param Exception    $previous
     */
    public function __construct($message = '', $values = null, $code = 0, Exception $previous = null)
    {
        $values = (array) $values;

        if ($values) {
            if (Arr::isAssociative($values)) {
                $message = strtr($message, $values);
            } else {
                array_unshift($values, $message);
                $message = call_user_func_array('sprintf', $values);
            }
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @static
     *
     * @param int    $code
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param array  $context
     *
     * @throws ErrorException
     * @return bool
     */
    public static function errorHandler($code, $message, $file = null, $line = null, $context = null)
    {
        if (error_reporting() & $code) {
            throw new ErrorException($message, $code, 0, $file, $line);
        }

        return false;
    }

    public static function shutdownHandler()
    {
        if ($error = error_get_last()) {
            ob_get_level() and ob_end_clean();

            static::exceptionHandler(
                new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line'])
            );

            exit(1);
        }
    }

    public static function exceptionHandler(Exception $e)
    {
        $class    = get_class($e);
        $code    = $e->getCode();
        $message = $e->getMessage();
        $file    = $e->getFile();
        $line    = $e->getLine();
        $trace   = $e->getTrace();

        ob_start();

        include Autoload::singleton()->findFile('views', 'core/error');

        echo ob_get_clean();

        exit(1);
    }


}
