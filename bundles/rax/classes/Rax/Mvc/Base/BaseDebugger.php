<?php

namespace Rax\Mvc\Base;

use ErrorException;
use Exception;
use Rax\Mvc\Cfs;
use Rax\Mvc\Debug;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseDebugger
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
     * @var Cfs
     */
    protected $cfs;

    public function __construct(Cfs $cfs)
    {
        $this->cfs = $cfs;
    }

    /**
     * Handles runtime errors.
     *
     * For better debugging, runtime errors are transformed into exceptions so
     * they may be handled by the exception handler.
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
    public function handleError($code, $message, $file = null, $line = null)
    {
        if (error_reporting() & $code) {
            throw new ErrorException($message, $code, 0, $file, $line);
        }

        // Returning true will bypass the standard PHP error handler
        return true;
    }

    /**
     * Handles shutdown errors.
     *
     * For better debugging, errors are transformed into exceptions so they may
     * be handled by the exception handler.
     *
     * Transforms and forwards fatal errors to the exception handler for
     * better debugging.
     */
    public function handleShutdown()
    {
        if ($error = error_get_last()) {
            ob_get_level() && ob_end_clean();

            $this->handleException(new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));
        }
    }

    /**
     * Pretty
     * Handles all exceptions, logging and highlighting the problematic line
     * for easier debugging, dies therefore after.
     *
     * @param Exception $e
     */
    public function handleException(Exception $e)
    {
//        $test = 'lol';
//        \Rax\Mvc\Debug::dump(Debug::varName($test));
        $class   = get_class($e);
        $code    = $e->getCode();
        $message = $e->getMessage();
        $file    = $e->getFile();
        $line    = $e->getLine();
        $trace = Debug::trace($e->getTrace());

//        \Rax\Mvc\Debug::dump($e->getTraceAsString());

        array_unshift($trace, array(
            'class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'message' => $e->getMessage(),
            'source' => Debug::highlightSourceCode($file, $line, 8),
            'call' => (($e instanceof ErrorException) ? static::$levels[$code] : $class).'<i class="icon-bolt"></i>',
        ));

//        \Rax\Mvc\Debug::dump($trace);

        // todo change to to include into a variable?
        ob_start();
        include $this->cfs->findFile('views', 'core/error2');
        echo ob_get_clean();
        exit(1);
    }
}
