<?php

/**
 * Your URL routes.
 *
 * FYI:
 *
 * - To make a segment optional, define a default value.
 * - To make a segment required, omit a default value.
 * - Routes are dispatched on a first-come first-serve basis.
 * - Use regular expressions to limit what a segment matches, the default regex
 *   for a segment is something similar to [^/]+ (which is pretty broad.)
 *
 *     // e.g. Force the id segment to match digits only, and make it required
 *     'default' => array(
 *         'pattern'  => '/{controller}/{action}/{id}',
 *         'defaults' => array(
 *             'controller' => 'default',
 *             'action'     => 'index',
 *         ),
 *         'rules' => array(
 *             'id' => '\d+',
 *         ),
 *     ),
 *
 * - Special segments are made available for you to tweak: controller, action,
 *   method, format, locale
 *
 *     // e.g.
 *     'rules' => array(
 *         'method' => 'POST', // Route will only match POST requests
 *     ),
 */
return array(
    'default' => array(
        'pattern'  => '/{controller}/{action}/{id}',
        'defaults' => array(
            'controller' => 'default',
            'action'     => 'index',
            'id'         => null,
        ),
    ),
);
