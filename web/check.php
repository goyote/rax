<?php

if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1'))) {
    header('HTTP/1.0 403 Forbidden');
    exit(1);
}

if (isset($_SERVER['HTTP_HOST'])) {
    require dirname(__DIR__).'/bin/check/web.php';
} else {
    require dirname(__DIR__).'/bin/check/cli.php';
}
