<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$fiddle = new \PhpFiddler\Fiddle();

$app->get('/', function() {
    return 'Hello world';
});

$app->get('/examples/{name}', function($name) use ($app, $fiddle) {
    ob_start();
    $fiddle->start();
    include (__DIR__ . '/../examples/' . $name . '.php');
    $fiddle->end();
    $body = ob_get_clean();

    return $body;
});

$app->get('/fiddles/{name}', function($name) use ($app, $fiddle) {
    ob_start();
    $fiddle->start();
    include (__DIR__ . '/../fiddles/' . $name . '.php');
    $fiddle->end();
    $body = ob_get_clean();

    return $body;
});

$app->run();

