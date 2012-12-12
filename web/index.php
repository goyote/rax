<?php

/*
 * This file is part of the Rax PHP framework.
 *
 * (c) Gregorio Ramirez <goyocode@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

/**
 * Capture the current time and memory usage. We'll use this in conjunction with
 * the final numbers to benchmark the framework.
 */
define('RAX_START_TIME',   microtime(true));
define('RAX_START_MEMORY', memory_get_peak_usage(true));
define('RAX_VERSION',      '0.1');

/**
 * Define the paths to the top level directories.
 */
define('ROOT_DIR',    realpath('..').'/');
define('BIN_DIR',     ROOT_DIR.'bin/');
define('BUNDLES_DIR', ROOT_DIR.'bundles/');
define('VENDOR_DIR',  ROOT_DIR.'vendor/');
define('WEB_DIR',     ROOT_DIR.'web/');
define('STORAGE_DIR', ROOT_DIR.'storage/');
define('CACHE_DIR',   STORAGE_DIR.'cache/');
define('LOG_DIR',     STORAGE_DIR.'log/');

/**
 * These are the only two hardcoded require()s, from now on the autoloader will
 * step up and autoload subsequent PHP classes.
 */
require BUNDLES_DIR.'rax/classes/Rax/Autoload.php';
require BUNDLES_DIR.'app/classes/Autoload.php';

Autoload::getSingleton()
    ->setBundles(array(
        'App' => BUNDLES_DIR.'app',
        'Rax' => BUNDLES_DIR.'rax',
    ))
    ->setIncludePath(VENDOR_DIR)
    ->register();

/**
 * Prepends the vendor directory to the include path for easy require()s of
 * misc classes.
 */
set_include_path(VENDOR_DIR.PATH_SEPARATOR.get_include_path());

/**
 * The application environment can be defined at the server level:
 *
 * - Apache: SetEnv APP_ENV development
 * - Nginx:  fastcgi_param APP_ENV development
 * - Shell:  export APP_ENV=development
 */
if (empty($_SERVER['APP_ENV'])) {
    throw new RuntimeException('Application environment was not defined');
}
Environment::set($_SERVER['APP_ENV']);

/**
 * "-1" reports all current and future errors.
 *
 * Ideally we show these in development and hide but log them in production.
 */
error_reporting(-1);

/**
 * All errors and exceptions are handled. You can set the logging threshold in
 * the configuration.
 */
if (Environment::isDev()) {
    ini_set('display_errors', 1);
    set_error_handler(array('Error', 'handleError'));
    register_shutdown_function(array('Error', 'handleShutdown'));
    set_exception_handler(array('Error', 'handleException'));
} else {
    ini_set('display_errors', 0);
}

/**
 * Sets the default time zone.
 *
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set(Config::get('kernel.timezone'));

$kernel = new Kernel();
$kernel->setRouter(new Router(Route::parse(Config::get('routes'))));

$request = new Request($_GET, $_POST, $_SERVER, array(), Config::get('request'));
Request::setSingleton($request);

$response = $kernel->process($request);
$response->send();
