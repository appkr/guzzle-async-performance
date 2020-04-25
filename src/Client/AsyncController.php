<?php

namespace Client;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AsyncController
{
    public function __invoke(): Response
    {
        $service = new DomainService();
        $resource = $service->async();

        return new JsonResponse($resource);
    }
}