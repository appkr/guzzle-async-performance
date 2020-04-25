<?php

namespace Client;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SyncController
{
    public function __invoke(): Response
    {
        $service = new DomainService();
        $resource = $service->sync();

        return new JsonResponse($resource);
    }
}