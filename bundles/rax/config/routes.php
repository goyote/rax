<?php

/**
 * Your URL routes.
 *
 * FYI:
 *
 * - To make a segment optional, define a default value.
 * - To make a segment required, omit a default value.
 * - Routes are dispatched on a first-come first-serve basis, parsed from top
 *   to bottom.
 * - Use regular expressions to limit what a segment matches, the default regex
 *   for a segment is something very similar to [^/]+ (which is pretty broad.)
 *
 *     // e.g. Add a required "page" segment that matches digits only
 *     'default' => array(
 *         'pattern'  => 'blog/<page>',
 *         'defaults' => array(
 *             'controller' => 'blog',
 *             'action'     => 'page',
 *             'page'       => 1,
 *         ),
 *         'rules' => array(
 *             'page' => '\d+',
 *         ),
 *     ),
 *
 * - "Special segments" are made available for you to define defaults or expose
 *   them to be altered dynamically: <controller>, <action>, <format>, <locale>
 * - "Special rules": ajax, secure, method, clientIp, serverIp, environment,
 *   auth, acl
 *
 *     // e.g.
 *     'rules' => array(
 *         'method'      => 'POST',        // Route will only match POST requests
 *         'ajax'        => true,          // Route will only match Ajax requests
 *         'environment' => 'development', // Route will only when in development
 *         ...
 *     ),
 */
return array(
    'default' => array(
        'pattern'  => '<controller>/<action>',
        'defaults' => array(
            'controller' => 'default',
            'action'     => 'index',
        ),
    ),
);
