<?php

use Rax\Data\Config;
use Rax\Http\Request;
use Rax\Mvc\Cfs;
use Rax\Mvc\MatchedRoute;
use Rax\Mvc\Service;

Service::getShared()
    ->set('twig', function (Cfs $cfs, Config $config, Request $request){
        $loader = new Twig_Loader_Filesystem($cfs->findDirs('views'));
        $env    = new Twig_Environment($loader, $config->get('twig')->asArray());
        $env->addGlobal('request', $request);

        return $env;
    })
    ->set('view', function(Service $service, MatchedRoute $matchedRoute) {
        return $service->build($matchedRoute->getViewClassName());
    })
;
