<?php

use Rax\Mvc\ServerMode;

/**
 *
 */
return array(
    /**
     *
     */
    'debug'               => true,

    /**
     *
     */
    'auto_reload'         => true,

    /**
     *
     */
    'strict_variables'    => true,

    /**
     *
     */
    'cache'               => CACHE_DIR.'twig',

    /**
     *
     */
    'charset'             => 'UTF-8',

    /**
     *
     */
    'base_template_class' => 'Twig_Template',

    /**
     *
     */
    'autoescape'          => 'html',

    /**
     *
     */
    'optimizations'       => -1,
);
