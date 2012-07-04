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

define('RAX_START_TIME',   microtime(true));
define('RAX_START_MEMORY', memory_get_peak_usage(true));

define('ROOT_DIR',    realpath('..').'/');
define('APP_DIR',     ROOT_DIR.'app/');
define('BUNDLES_DIR', ROOT_DIR.'bundles/');
define('LIB_DIR',     ROOT_DIR.'lib/');
define('VENDOR_DIR',  ROOT_DIR.'vendor/');
define('WEB_DIR',     ROOT_DIR.'web/');

require LIB_DIR.'classes/core/autoload.php';
require APP_DIR.'classes/autoload.php';

$autoload = Autoload::getSingleton();
$autoload->setBundles(array(
    'Auth' => BUNDLES_DIR.'auth',
));
$autoload->setCascadingFilesystem(array(
    APP_DIR,
    $autoload->getBundles(),
    LIB_DIR,
));
$autoload->register();
$autoload->setIncludePath(VENDOR_DIR);

if (isset($_SERVER['APP_ENV'])) {
    Environment::set(constant('Environment::'.strtoupper($_SERVER['APP_ENV'])));
} else {
    throw new Exception('Could not determine the server environment');
}

$config = Config::get('kernel');

echo $config->foo;

unset($autoload, $config);
