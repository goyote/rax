<?php

/**
 * @package   Rax
 * @copyright Copyright (c) 2012 Gregorio Ramirez <goyocode@gmail.com>
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
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
     * Transforms notices and simple errors into an exception for better debugging.
     *
     * @throws ErrorException
     *
     * @param int    $code
     * @param string $message
     * @param string $file
     * @param int    $line
     *
     * @return bool
     */
    public static function handleError($code, $message, $file = null, $line = null)
    {
        if (error_reporting() & $code) {
            throw new ErrorException($message, $code, 0, $file, $line);
        }

        return false;
    }

    /**
     * Transforms fatal errors into exceptions for better debugging.
     */
    public static function handleShutdown()
    {
        /** @noinspection PhpAssignmentInConditionInspection */
        if ($error = error_get_last()) {
            $level = ob_get_level();
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            static::handleException(
                new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line'])
            );
        }
    }

    /**
     * Handles all exceptions, logging and highlighting the problematic line
     * for easier debugging, dies therefore after.
     *
     * @param Exception $e
     */
    public static function handleException(Exception $e)
    {
        require 'markdown.php';

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
