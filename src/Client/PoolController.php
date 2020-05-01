<?php

namespace Client;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PoolController
{
    public function __invoke(): Response
    {
        $service = new DomainService();
        $isSuccess = $service->pool();

        return new JsonResponse($isSuccess);
    }
}