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
 *     // e.g. Add a required id segment that matches digits only
 *     'default' => array(
 *         'pattern'  => '<controller>/<action>/<id>',
 *         'defaults' => array(
 *             'controller' => 'default',
 *             'action'     => 'index',
 *         ),
 *         'rules' => array(
 *             'id' => '\d+',
 *         ),
 *     ),
 *
 * - "Special segments" are made available for you to define defaults or expose
 *   them to be altered dynamically: <controller>, <action>, <format>, <locale>
 * - "Special rules": ajax, secure, method, clientIp, serverIp
 *
 *     // e.g.
 *     'rules' => array(
 *         'method' => 'POST', // Route will only match POST requests
 *         'ajax'   => true,   // Route will only match Ajax requests
 *         'secure' => true,   // Route will only match https
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
