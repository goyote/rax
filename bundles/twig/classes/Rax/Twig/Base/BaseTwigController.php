<?php

namespace Rax\Twig\Base;

use Rax\Http\Response;
use Rax\Mvc\Controller;
use Rax\Mvc\MatchedRoute;
use Rax\Mvc\Service;
use Twig_Environment;

/**
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseTwigController extends Controller
{
    /**
     * @param Response         $response
     * @param Service          $service
     * @param Twig_Environment $twig
     * @param MatchedRoute     $matchedRoute
     */
    public function __after(Response $response, Service $service, Twig_Environment $twig, MatchedRoute $matchedRoute)
    {
        $response->setContent($twig->render($matchedRoute->getTemplateName().'.twig', array('view' => $service->view)));
    }
}
