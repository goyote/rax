<?php

class Core_Benchmark
{
    protected static $benchmarks = array();

    public static function start($benchmark = 'default')
    {
        static::$benchmarks[$benchmark] = array(
            'startTime'   => microtime(true),
            'startMemory' => memory_get_peak_usage(true),
        );
    }

    public static function stop($benchmark = 'default')
    {

    }
}
