<?php

/*
 * This file is part of the Rax PHP framework.
 *
 * (c) Gregorio Ramirez <goyocode@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

/**
 * Capture the current time and memory usage.
 *
 * We'll use this in conjunction with the final numbers to benchmark the framework.
 */
define('RAX_START_TIME',   microtime(true));
define('RAX_START_MEMORY', memory_get_peak_usage(true));

/**
 *
 */
define('ROOT_DIR',    realpath('..').'/');
define('APP_DIR',     ROOT_DIR.'app/');
define('BUNDLES_DIR', ROOT_DIR.'bundles/');
define('SRC_DIR',     ROOT_DIR.'src/');
define('VENDOR_DIR',  ROOT_DIR.'vendor/');
define('WEB_DIR',     ROOT_DIR.'web/');

require SRC_DIR.'classes/core/autoload.php';
require APP_DIR.'classes/Autoload.php';

Autoload::singleton()
    ->bundles(array(
        'Auth' => BUNDLES_DIR.'auth',
    ))
    ->cascadingFilesystem(array(
        APP_DIR,
        Autoload::singleton()->bundles(),
        SRC_DIR,
    ))
    ->includePath(VENDOR_DIR)
    ->register();

set_include_path(VENDOR_DIR.PATH_SEPARATOR.get_include_path());

if (isset($_SERVER['APP_ENV'])) {
    Environment::set(constant('Environment::'.strtoupper($_SERVER['APP_ENV'])));
} else {
    throw new Exception('Could not determine the server environment');
}

$a = array('foo' => 'foo', 'bar' => 'bar', 'hi' => 'ho', 'lol' => 'lol');
$b = array('foo' => array('bar' => null, 'two' => 6, 7 => 77), 'bar' => 'bar', 'lol' => 'lol', null => '5435');
$c = new ArrayObject($b);
$d = '32';

Debug::dump(array('foo', 'bar') + array('lol', 'baz', 'ok'));

//echo $c[0];
//Debug::dump(array_unshift($a, 'lol'));
Arr::unshift($a, 'hi', 'ho');
Debug::dump($a);

//Debug::dump(Arr::get($b, 'foo.7.o'));
