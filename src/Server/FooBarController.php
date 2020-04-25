<?php

namespace Server;

use Common\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FooBarController
{
    public function __invoke(): Response
    {
        $logger = Logger::create('server.FooBarController', __DIR__.'/../../logs/server.log');
        $logger->info('request received');

        usleep(10000); // 10ms
        return new JsonResponse(['foo' => 'bar']);
    }
}