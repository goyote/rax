<?php

namespace Rax\Generator\Controller;

use Rax\Generator\ServiceContainerGenerator;
use Rax\Mvc\Controller;

class ServiceController extends Controller
{
    /**
     * @param ServiceContainerGenerator $generator
     */
    public function updateAction(ServiceContainerGenerator $generator)
    {
        $generator
            ->setSaveFile(APP_DIR.'classes/Rax/Mvc/ServiceContainer.php')
            ->generate()
        ;

        exit();
    }
}
