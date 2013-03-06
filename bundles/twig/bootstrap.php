<?php

use Rax\Data\Config;
use Rax\Http\Request;
use Rax\Mvc\Cfs;
use Rax\Mvc\RouteMatch;
use Rax\Mvc\ServiceContainer;

ServiceContainer::getShared()
    ->set('twig', function(Cfs $cfs, Config $config, Request $request) {
        $twigLoader      = new Twig_Loader_Filesystem($cfs->findDirs('views'));
        $twigEnvironment = new Twig_Environment($twigLoader, $config->get('twig')->asArray());
        $twigEnvironment->addGlobal('request', $request);

        return $twigEnvironment;
    })
    ->set('view', function(ServiceContainer $service, RouteMatch $routeMatch) {
        return $service->build($routeMatch->getViewClassName());
    })
;
