<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Rax\Data\Config;
use Rax\Mvc\Cfs;
use Rax\Mvc\ServerMode;
use Rax\Mvc\Service;

Service::getShared()
    ->set('em', function(ServerMode $serverMode, Cfs $cfs, Config $config) {
        $setup = Setup::createConfiguration($serverMode->isDev(), $config->get('doctrine.proxyDir'));
        $setup->setMetadataDriverImpl(new PhpDriver($cfs->findDirs('schema')));

        return EntityManager::create($config->get('database.default'), $setup);
    })
    ->set('repository', function(EntityManager $em, $className) {
        return $em->getRepository($className);
    })
;
