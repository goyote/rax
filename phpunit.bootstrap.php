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
use Rax\Mvc\Autoload;
use Rax\Mvc\Cfs;
use Rax\Mvc\ServerMode;
use Rax\Data\Config;
use Rax\Mvc\Kernel;
use Rax\Routing\Route;
use Rax\Routing\Router;

// initial snapshot used for benchmarking
define('RAX_START_TIME',   microtime(true));
define('RAX_START_MEMORY', memory_get_peak_usage(true));

// top level directory paths
define('ROOT_DIR',    realpath(__DIR__).'/');
define('BUNDLES_DIR', ROOT_DIR.'bundles/');
define('APP_DIR',     BUNDLES_DIR.'app/');
define('RAX_DIR',     BUNDLES_DIR.'rax/');
define('VENDOR_DIR',  ROOT_DIR.'vendor/');
define('WEB_DIR',     ROOT_DIR.'web/');
define('STORAGE_DIR', ROOT_DIR.'storage/');
define('CACHE_DIR',   STORAGE_DIR.'cache/');
define('LOG_DIR',     STORAGE_DIR.'log/');

/**
 * @param array $classes
 */
function loadClasses(array $classes)
{
    foreach($classes as $class) {
        require RAX_DIR.'classes/Rax/Mvc/Base/Base'.$class.'.php';
        if (is_file($file = APP_DIR.'classes/Rax/Mvc/'.$class.'.php')) {
            /** @noinspection PhpIncludeInspection */
            require $file;
        } else {
            require RAX_DIR.'classes/Rax/Mvc/'.$class.'.php';
        }
    }
}

// hardcoded requires needed before the autoloader kicks in
loadClasses(array(
    'Environment',
    'Cfs',
    'Autoload',
    'Exception',
));

/**
 * Prepends the vendor directory to the include path for easy require()s of
 * miscellaneous classes.
 */
set_include_path(VENDOR_DIR.PATH_SEPARATOR.get_include_path());

/**
 * The application environment can be defined at the server level:
 *
 * - Apache: SetEnv ServerMode development
 * - Nginx:  fastcgi_param ServerMode development
 * - Shell:  export ServerMode=development
 */
if (empty($_SERVER['ServerMode'])) {
    throw new RuntimeException('Application environment was not defined');
}
ServerMode::set($_SERVER['ServerMode']);

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
if (ServerMode::isDev()) {
    ini_set('display_errors', 1);
    set_error_handler(array('Rax\Mvc\Exception', 'handleError'));
    register_shutdown_function(array('Rax\Mvc\Exception', 'handleShutdown'));
    set_exception_handler(array('Rax\Mvc\Exception', 'handleException'));
} else {
    ini_set('display_errors', 0);
}

$cfs = Cfs::setSingleton(function() {
    return Cfs::create()
        ->setFileExtensions(array(
            'generated.php',
            ServerMode::getName().'.php',
            ServerMode::getShortName().'.php',
            'php',
        ))
        ->loadBundles(APP_DIR.'config/bundles');
});

Autoload::setSingleton(function() use ($cfs) {
    return Autoload::create()
        ->setCfs($cfs)
        ->setClassMap(require VENDOR_DIR.'composer/autoload_classmap.php')
        ->setNamespaces(require VENDOR_DIR.'composer/autoload_namespaces.php')
        ->register();
});
