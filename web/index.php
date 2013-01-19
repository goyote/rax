<?php

/*
 * The front controller of the Rax PHP framework.
 *
 * (c) Gregorio Ramirez <goyocode@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

use Rax\Http\Request;
use Rax\Loader\Autoload;
use Rax\Mvc\Cfs;
use Rax\Mvc\Environment;
use Rax\Data\Config;
use Rax\Mvc\Kernel;
use Rax\Mvc\Route;
use Rax\Mvc\Router;

// initial snapshot used for benchmarking
define('RAX_START_TIME', microtime(true));
define('RAX_START_MEMORY', memory_get_peak_usage(true));

// top level directory paths
define('ROOT_DIR', realpath('..').'/');
define('BUNDLES_DIR', ROOT_DIR.'bundles/');
define('APP_DIR', BUNDLES_DIR.'app/');
define('RAX_DIR', BUNDLES_DIR.'rax/');
define('VENDOR_DIR', ROOT_DIR.'vendor/');
define('WEB_DIR', ROOT_DIR.'web/');
define('STORAGE_DIR', ROOT_DIR.'storage/');
define('CACHE_DIR', STORAGE_DIR.'cache/');
define('LOG_DIR', STORAGE_DIR.'log/');

// todo generate bootstrap.php.cache
// hardcoded requires needed before the autoloader kicks in
require RAX_DIR.'classes/Rax/Mvc/Base/BaseEnvironment.php';
if (is_file($file = APP_DIR.'classes/Rax/Mvc/Environment.php')) {
    /** @noinspection PhpIncludeInspection */
    require $file;
} else {
    require RAX_DIR.'classes/Rax/Mvc/Environment.php';
}
require RAX_DIR.'classes/Rax/Mvc/Base/BaseCfs.php';
if (is_file($file = APP_DIR.'classes/Rax/Mvc/Cfs.php')) {
    /** @noinspection PhpIncludeInspection */
    require $file;
} else {
    require RAX_DIR.'classes/Rax/Mvc/Cfs.php';
}
require RAX_DIR.'classes/Rax/Loader/Base/BaseAutoload.php';
if (is_file($file = APP_DIR.'classes/Rax/Loader/Autoload.php')) {
    /** @noinspection PhpIncludeInspection */
    require $file;
} else {
    require RAX_DIR.'classes/Rax/Loader/Autoload.php';
}
require RAX_DIR.'classes/Rax/Mvc/Base/BaseException.php';
if (is_file($file = APP_DIR.'classes/Rax/Mvc/Exception.php')) {
    /** @noinspection PhpIncludeInspection */
    require $file;
} else {
    require RAX_DIR.'classes/Rax/Mvc/Exception.php';
}

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
    set_error_handler(array('Rax\Mvc\Exception', 'handleError'));
    register_shutdown_function(array('Rax\Mvc\Exception', 'handleShutdown'));
    set_exception_handler(array('Rax\Mvc\Exception', 'handleException'));
} else {
    ini_set('display_errors', 0);
}

$cfs = Cfs::setSingleton(function () {
    return Cfs::create()
        ->setFileExtension(array(
            'generated.php',
            Environment::getName().'.php',
            Environment::getShortName().'.php',
            'php',
        ))
        ->loadBundles(APP_DIR.'config/bundles');
});

Autoload::setSingleton(function () use ($cfs) {
    return Autoload::create()
        ->setCfs($cfs)
        ->setClassMap(require VENDOR_DIR.'composer/autoload_classmap.php')
        ->setNamespaces(require VENDOR_DIR.'composer/autoload_namespaces.php')
        ->register();
});

//$cfs->bootstrapBundles();

//Debug::dump(Config::get('twig'));

/**
 * Sets the default time zone.
 *
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set(Config::get('kernel.timezone'));

$router  = new Router(Route::parse(Config::get('routes')));
$request = new Request($_GET, $_POST, $_SERVER, array(), Config::get('request'));

$kernel = new Kernel();
$kernel->setRouter($router);
$kernel->setRequest($request);

$response = $kernel->process();
$response->send();
