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
    throw new Error('Could not determine the server environment');
}

/**
 * Reports all current and future errors.
 *
 * Don't suppress errors, fix them.
 */
error_reporting(-1);

/**
 *
 */
if (Environment::isDev()) {
    ini_set('display_errors', 1);
    set_error_handler(array('Error', 'handleError'));
    register_shutdown_function(array('Error', 'handleShutdown'));
    set_exception_handler(array('Error', 'handleException'));
} else {
    ini_set('display_errors', 0);
}

include 'i.php';

echo 'yes';
