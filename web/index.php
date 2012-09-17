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
define('BUNDLES_DIR', ROOT_DIR.'bundles/');
define('VENDOR_DIR',  ROOT_DIR.'vendor/');
define('WEB_DIR',     ROOT_DIR.'web/');

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
 * Prepends the vendor directory to the include path for easy require()s.
 */
set_include_path(VENDOR_DIR.PATH_SEPARATOR.get_include_path());

/**
 * Sets the application environment.
 *
 * The application environment can be defined at the server level:
 *
 * - Apache: SetEnv APP_ENV development
 * - Nginx:  fastcgi_param APP_ENV development
 * - Shell:  export APP_ENV=development
 */
if (isset($_SERVER['APP_ENV'])) {
    Environment::set($_SERVER['APP_ENV']);
} else {
    throw new RuntimeException('Could not determine the application environment');
}

/**
 * "-1" reports all current and future errors.
 *
 * Generally speaking, it's a bad idea to suppress errors. Ideally they should
 * be shown in development and hidden and logged in production.
 */
error_reporting(-1);

/**
 * All errors will be handled and displayed in development.
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

include 'i.php';

Debug::dump($_SERVER);

Kernel::getSingleton()
    ->handleRequest(new Request($_GET, $_POST, $_SERVER, array(), Config::get('request')))
    ->sendResponse();
