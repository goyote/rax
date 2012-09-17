<?php

/**
 *
 */
class Rax_Benchmark
{
    protected static $benchmarks = array();

    /**
     * @param string $benchmark
     */
    public static function start($benchmark = 'default')
    {
        static::$benchmarks[$benchmark] = array(
            'startTime'   => microtime(true),
            'startMemory' => memory_get_peak_usage(true),
        );
    }

    /**
     * @param string $benchmark
     */
    public static function stop($benchmark = 'default')
    {

    }
}
