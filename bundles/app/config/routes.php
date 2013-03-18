<?php

/**
 * Your URL routes.
 *
 * FYI:
 *
 * - Routes are read from top to bottom and dispatched on a first-come first-serve basis.
 * - To make a <segment> required, omit a default value in the "defaults" array.
 * - To make a <segment> optional, define a default value in the "defaults" array.
 * - To limit what a <segment> matches, define a regex in the "rules" array.
 * - To match a certain condition use a filter: ajax, secure, method, clientIp,
 *   serverIp, serverMode, auth, acl & host.
 *
 *     // Random example
 *     'route-id' => array(
 *         'path'       => '/blog/<page>',
 *         'controller' => 'App\Controller\Blog:index',
 *         'defaults'   => array(
 *             'page' => 1,
 *         ),
 *         'rules' => array(
 *             'page' => '\d+',
 *         ),
 *         'filter' => array(
 *             'method'     => 'get',
 *             'serverMode' => 'dev',
 *         ),
 *     ),
 *
 * @see todo
 */
return array(
    'default' => array(
        'path'       => '/',
        'controller' => 'App\Controller\Home:index',
    ),
);
