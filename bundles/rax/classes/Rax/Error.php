<?php

/**
 *
 */
class Rax_Error extends Exception
{
    /**
     * @var array
     */
    public static $levels = array(
        E_ERROR             => 'Fatal Error',
        E_NOTICE            => 'Notice',
        E_WARNING           => 'Warning',
        E_DEPRECATED        => 'Deprecated',
        E_PARSE             => 'Parse Error',
        E_STRICT            => 'Runtime Notice',
        E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        E_USER_ERROR        => 'User Error',
        E_USER_NOTICE       => 'User Notice',
        E_USER_WARNING      => 'User Warning',
        E_USER_DEPRECATED   => 'User Deprecated',
    );

    /**
     * @param string        $message
     * @param string|array  $values
     * @param int           $code
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
     *
     * @throws \ErrorException
     * @return bool
     */
    public static function handleError($code, $message, $file = null, $line = null)
    {
        if (error_reporting() & $code) {
            throw new \ErrorException($message, $code, 0, $file, $line);
        }

        return false;
    }

    /**
     *
     */
    public static function handleShutdown()
    {
        /** @noinspection PhpAssignmentInConditionInspection */
        if ($error = error_get_last()) {
            ob_get_level() and ob_end_clean();

            static::handleException(new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));

            exit(1);
        }
    }

    /**
     * @param Exception $e
     */
    public static function handleException(Exception $e)
    {
        $class   = get_class($e);
        $code    = $e->getCode();
        $message = $e->getMessage();
        $file    = $e->getFile();
        $line    = $e->getLine();
        $trace   = Debug::trace($e->getTrace());

        ob_start();

        include Autoload::getSingleton()->findFile('views', 'core/error');

        echo ob_get_clean();

        exit(1);
    }
}
