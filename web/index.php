<?php
require_once __DIR__ . '/../vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;
use Silex\Provider\TwigServiceProvider;

$app = new Silex\Application();
$fiddle = new \PhpFiddler\Fiddle();
$app->register(new TwigServiceProvider, [
        'twig.path' => __DIR__ . '/views',
    ]);
$app['twig']->addExtension(new Twig_Extension_Debug());

$app->get('/', function() use ($app) {
    try {
        return $app['twig']->render('main.html.twig');
    } catch(\Exception $exc) {
        return '<pre>'.$exc->getMessage().'</pre>';
    }
})->bind('home');

$app->get('/about', function() use ($app) {
    $converter = new CommonMarkConverter;
    $body = $converter->convertToHtml(file_get_contents(__DIR__ . '/../README.md'));
    return $app['twig']->render('about.html.twig', ['about' => $body,]);
})->bind('about');

$app->get('/examples', function() use ($app) {
    return $app['twig']->render('main.html.twig', ['title' => 'Examples',]);
})->bind('examples');

$app->get('/examples/{name}', function($name) use ($app, $fiddle) {
    ob_start();
    include (__DIR__ . '/../examples/' . $name . '.php');
    $body = ob_get_clean();

    return $app['twig']->render('main.html.twig', ['title' => 'Example: ' . $name, 'body' => $body]);
})->bind('example');

$app->get('/fiddles', function() use ($app) {
    return $app['twig']->render('main.html.twig', ['title' => 'Fiddles',]);
})->bind('fiddles');

$app->get('/fiddles/{name}', function($name) use ($app, $fiddle) {
    ob_start();
    include (__DIR__ . '/../fiddles/' . $name . '.php');
    $body = ob_get_clean();

    return $app['twig']->render('main.html.twig', ['title' => 'Fiddle: ' . $name, 'body' => $body]);
})->bind('fiddle');

$app->run();

