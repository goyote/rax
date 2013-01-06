<?php

/**
 * Your URL routes.
 *
 * FYI:
 *
 * - To make a <segment> optional, define a default value in the "defaults" array.
 * - To make a <segment> required, omit a default value in the "defaults" array.
 * - Routes are read from top to bottom and dispatched on a first-come first-serve basis.
 * - Use regular expressions to limit what a <segment> matches, these go in the
 *   "rules" array. The default regex for a <segment> looks something very similar
 *   to [^/]+ (which is pretty broad.)
 * - "Special segments" are made available for you to define default values or
 *   expose through the pattern string to be altered dynamically: <controller>,
 *   <action>, <format>, <locale>
 * - "Special rules" are available to filter requests: ajax, secure, method,
 *   clientIp, serverIp, environment, auth, acl
 *
 * @see todo
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
