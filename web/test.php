<?php

function benchmark($function) {
    $time = microtime(true);
    for ($i = 0; $i < 300000; $i++) {
        $function();
    }

    $total = microtime(true) - $time;
    $total = round($total, 2).'s';

    echo '<pre>';
    echo $total, PHP_EOL;
    $milliseconds = $total * 1000;
    printf("TIME: %0.2dms\n", $milliseconds);
    echo "\n";
}

//$key = 'app.foo.bar';
//
//benchmark(function() use ($key) {
//    $tmp = strstr($key, '.', true);
//});
//
//benchmark(function() use ($key) {
//    $tmp = current(explode('.', $key));
//});
//benchmark(function() {
//    str_replace(array('<fileName>'), array('app'), 'C:\Users\Gregorio\Dropbox\Projects\rax/bundles/app/config/<fileName>.generated.php');
//});
//
//benchmark(function() {
//    strtr('C:\Users\Gregorio\Dropbox\Projects\rax/bundles/app/config/<fileName>.generated.php', array(
//        '<fileName>' => 'app',
//    ));
//});
//
//echo "\n";
//
//benchmark(function() {
//    str_replace('<fileName>', 'app', 'C:\Users\Gregorio\Dropbox\Projects\rax/bundles/app/config/<fileName>.generated.php');
//});
//
//benchmark(function() {
//    strtr('C:\Users\Gregorio\Dropbox\Projects\rax/bundles/app/config/<fileName>.generated.php', '<fileName>', 'app');
//});

echo strrchr('C:\Users\Gregorio\Dropbox\Projects\rax/bundles/app/config/<name>.generated.php', '<name>');
echo "\n";
echo strstr('C:\Users\Gregorio\Dropbox\Projects\rax/bundles/app/config/<name>.generated.php', '<name>');
echo "\n";
$file = 'C:\Users\Gregorio\Dropbox\Projects\rax/bundles/app/config/<name>.generated.php';
echo substr($file, strrpos($file, '<name>') + strlen('<name>') + 1);
