<?php

/**
 * Your database connections.
 *
 * Use this file to store generic database information that may be used across
 * all environments. Then store the environment specific information such as
 * credentials and host ips in the relevant environment configuration file.
 *
 * database.php < database.prod.php < database.staging.php
 *
 * @see http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
 * @see todo
 */
return array(
    'default' => array(
        'driver'  => 'pdo_mysql',
        'host'    => 'localhost',
        'port'    => '3306',
        'charset' => 'utf8',
        'dbname'  => 'rax',
    ),
);
