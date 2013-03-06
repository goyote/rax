<?php

namespace Rax\Mvc\Base;

use Rax\Mvc\ShutdownManager;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseShutdownManager
{
    /**
     * @var array
     */
    protected $callbacks = array();

    /**
     *
     */
    public function __construct()
    {
        register_shutdown_function(array($this, 'run'));
    }

    public function run()
    {
        foreach ($this->callbacks as $callback) {
            call_user_func($callback);
        }
    }

    /**
     * @param array $callbacks
     *
     * @return ShutdownManager
     */
    public function set(array $callbacks)
    {
        $this->callbacks = $callbacks;

        return $this;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->callbacks;
    }

    /**
     * @param mixed $callback
     *
     * @return ShutdownManager
     */
    public function add($callback)
    {
        $this->callbacks[] = $callback;

        return $this;
    }

    /**
     * @param mixed $callback
     *
     * @return ShutdownManager
     */
    public function remove($callback)
    {
        if (false !== ($key = array_search($callback, $this->callbacks, true))) {
            unset($this->callbacks[$key]);
        }

        return $this;
    }
}
