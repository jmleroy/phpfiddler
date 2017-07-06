<?php
require_once __DIR__ . '/../vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;

$app = new Silex\Application();
$fiddle = new \PhpFiddler\Fiddle();

$app->get('/', function() use ($fiddle) {
    ob_start();
    $fiddle->start();
    $converter = new CommonMarkConverter;
    echo '<div class="readme">';
    echo $converter->convertToHtml(file_get_contents(__DIR__ . '/../README.md'));
    echo '</div>';
    $fiddle->end();
    $body = ob_get_clean();

    return $body;
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

