<?php

/**
 *
 */
return array(
    /**
     *
     */
    'debug'               => Environment::isDev(),

    /**
     *
     */
    'auto_reload'         => Environment::isDev(),

    /**
     *
     */
    'strict_variables'    => Environment::isDev(),

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
