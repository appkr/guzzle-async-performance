<?php
/**
 * Front controller
 * Referenced from @see https://symfony.com/doc/current/create_framework/front_controller.html
 */

require __DIR__.'/../vendor/autoload.php';

date_default_timezone_set('Asia/Seoul');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$response = new Symfony\Component\HttpFoundation\Response();

$map = [
    '/' => Server\FooBarController::class,
    '/sync' => Client\SyncController::class,
    '/async' => Client\AsyncController::class,
    '/pool' => Client\PoolController::class,
];

$path = $request->getPathInfo();
if (isset($map[$path])) {
    try {
        $object = new $map[$path]();
        $response = $object();
    } catch (Throwable $e) {
        echo $e;
        $response->setStatusCode(404);
        $response->setContent('Not Found');
    }
} else {
    $response->setStatusCode(404);
    $response->setContent('Not Found');
}

$response->send();
