<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/', function() {
    return 'Hello world';
});

$app->get('/examples/{name}', function($name) use ($app) {
    ob_start();
    include (__DIR__ . '/../examples/' . $name . '.php');

    $body = ob_get_clean();

    return $body;
});

$app->get('/fiddles/{name}', function($name) use ($app) {
    ob_start();
    include (__DIR__ . '/../fiddles/' . $name . '.php');
    $body = ob_get_clean();

    return $body;
});

$app->run();

