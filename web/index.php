<?php

/*
 * The front controller of the Rax PHP framework.
 *
 * (c) Gregorio Ramirez <goyocode@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

use Doctrine\ORM\Tools\Setup;
use Rax\Mvc\Autoload;
use Rax\Mvc\Cfs;
use Rax\Mvc\Debugger;
use Rax\Mvc\ServerMode;
use Rax\Mvc\ServiceContainer;

/**
 * "Popular" constants.
 *
 * You may define more in your bundle's bootstrap file.
 */
define('EXT', '.php');
define('DS',  DIRECTORY_SEPARATOR);
define('PS',  PATH_SEPARATOR);

/**
 * Top level directory paths.
 */
define('ROOT_DIR',    dirname(__DIR__).'/');
define('BIN_DIR',     ROOT_DIR.'bin/');
define('BUNDLES_DIR', ROOT_DIR.'bundles/');
define('APP_DIR',     BUNDLES_DIR.'app/');
define('RAX_DIR',     BUNDLES_DIR.'rax/');
define('VENDOR_DIR',  ROOT_DIR.'vendor/');
define('WEB_DIR',     ROOT_DIR.'web/');
define('STORAGE_DIR', ROOT_DIR.'storage/');
define('CACHE_DIR',   STORAGE_DIR.'cache/');
define('LOG_DIR',     STORAGE_DIR.'log/');

/**
 * Hardcoded requires needed before the autoloader kicks in.
 */
foreach (array(
    'ServerMode',
    'Cfs',
    'Autoload',
) as $class) {
    require RAX_DIR.'classes/Rax/Mvc/Base/Base'.$class.'.php';
    if (is_file($file = APP_DIR.'classes/Rax/Mvc/'.$class.'.php')) {
        require $file;
    } else {
        require RAX_DIR.'classes/Rax/Mvc/'.$class.'.php';
    }
}

/**
 * Date manipulation should be done in UTC.
 *
 * @link todo
 */
date_default_timezone_set('UTC');

/**
 * The server mode can be defined at the server level:
 *
 * - Apache: SetEnv ServerMode development
 * - Nginx:  fastcgi_param ServerMode development
 * - Shell:  export ServerMode=development
 *
 * @link todo
 */
$serverMode = new ServerMode($_SERVER['ServerMode']);

/**
 * The Cascading Filesystem is used to determine which file(s) to load.
 *
 * @link todo
 */
$cfs = new Cfs($serverMode);
$cfs->loadBundles(APP_DIR.'config/bundles');

/**
 * The autoloader uses Composer to install new bundles and third party libraries.
 *
 * @link todo
 */
$autoload = new Autoload($cfs);
$autoload->setClassMap(require VENDOR_DIR.'composer/autoload_classmap.php');
$autoload->setNamespaces(require VENDOR_DIR.'composer/autoload_namespaces.php');
$autoload->register();

/**
 * The service container builds the objects and injects its dependencies.
 *
 * @link todo
 */
$service = ServiceContainer::getShared();
$service->debugger = new Debugger($cfs);
$service->set(compact('serverMode', 'cfs', 'autoload', 'service'));

/**
 * Prepends the vendor directory to the include path for easy require()s of
 * miscellaneous files.
 */
set_include_path(VENDOR_DIR.PS.get_include_path());

/**
 * A value of "-1" reports all current and future errors.
 */
error_reporting(-1);

/**
 * We show errors in dev(), but hide and log them in prod().
 *
 * @link todo
 */
ini_set('display_errors', $serverMode->isDev());

set_error_handler(array($service->debugger, 'handleError'));
register_shutdown_function(array($service->debugger, 'handleShutdown'));
set_exception_handler(array($service->debugger, 'handleException'));

/**
 * Bootstraps each bundle.
 *
 * @link todo
 */
$cfs->bootstrap();

/**
 * Handles the request and serves the response.
 *
 * @link todo
 */
$service->kernel->process()->send();
