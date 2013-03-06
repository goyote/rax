<?php

use Rax\Data\Config;
use Rax\Data\Driver\FileDataDriver;
use Rax\Http\Request;
use Rax\Mvc\Cfs;
use Rax\Mvc\Kernel;
use Rax\Mvc\Route;
use Rax\Mvc\Router;
use Rax\Mvc\ServerMode;
use Rax\Mvc\ServiceContainer;
use Rax\Mvc\Validator\RouteValidator;

$service = ServiceContainer::getShared()
    ->set('config', function(Cfs $cfs) {
        $driver = new FileDataDriver();
        $driver->setCfs($cfs);
        $driver->setScanDir('config');
        $driver->setSaveFile(APP_DIR.'config/<name>.generated.php');
        $driver->setCanSave(false);

        $config = new Config();
        $config->addDriver($driver);

        return $config;
    })
    ->set('router', function(Config $config) {
        return new Router(Route::parse($config->get('routes')), new RouteValidator());
    })
    ->set('request', function(Config $config) {
        return new Request($_GET, $_POST, $_SERVER, array(), $config->get('request'));
    })
    ->set('kernel', function(Router $router, Request $request, Cfs $cfs, ServiceContainer $service, ServerMode $serverMode, Config $config) {
        return Kernel::create()
            ->setRouter($router)
            ->setRequest($request)
            ->setCfs($cfs)
            ->setService($service)
            ->setConfig($config)
            ->setServerMode($serverMode)
        ;
    })
;
