<?php

namespace Client;

use Common\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DomainService
{
    public function sync(): ?array
    {
        $request = new Request('GET', getenv("SERVER_URI"), [
            "accept" => "application/json",
        ]);

        try {
            $httpClient = $this->getLoggableClient('sync');
            $response = $httpClient->send($request);
        } catch (\Exception $e) {
            throw $e;
        }

        // Simulate expensive job
        usleep(10000); // 10ms

        return json_decode($response->getBody()->getContents(), true);
    }

    public function async(): ?array
    {
        $request = new Request('GET', getenv("SERVER_URI"), [
            "accept" => "application/json",
        ]);
        $httpClient = $this->getLoggableClient('async');
        $promise = $httpClient->sendAsync($request)
            ->then(function (ResponseInterface $response) use (&$promise) {
                $content = $response->getBody()->getContents();
                return json_decode($content, true);
            }, function (RequestException $e) {
                throw $e;
            });

        // Simulate expensive job
        usleep(10000); // 10ms

        return $promise->wait();
    }

    public function pool(): bool
    {
        $httpClient = $this->getLoggableClient('pool');

        // Simulate get a resource from a remote service
        $requests[] = new Request('GET', 'http://httpbin.org/get', [
            "accept" => "application/json",
        ]);

        // Simulate changing some state of a remote resource
        $requests[] = new Request('PUT', 'http://httpbin.org/put', [
            "accept" => "application/json",
            "content-type" => "application/json",
        ], json_encode(['foo' => 'bar']));

        $pool = new Pool($httpClient, $requests, [
            'fulfilled' => function (ResponseInterface $response, int $index) {
                $content = $response->getBody()->getContents();
                return json_decode($content, true);
            },
            'rejected' => function (RequestException $e, int $index) {
                $content = $e->getResponse()->getBody()->getContents();
                return json_decode($content, true);
            }
        ]);

        // Simulate expensive job
        usleep(500000); // 500ms

        $pool->promise()->wait();

        return true;
    }

    private function getLoggableClient(string $context): Client
    {
        $logger = Logger::create("client.DomainService.{$context}", __DIR__.'/../../logs/client.log');

        $afterMiddleware =  function (RequestInterface $req, array $options = [], PromiseInterface $promise = null) use ($logger) {
            $onFulfilledCallback = function (ResponseInterface $res) use ($req, $logger) {
                $logger->info('success', [
                    'request' => [
                        'uri' => "{$req->getMethod()} {$req->getUri()}",
                        'headers' => $this->simpleAssoc($req->getHeaders()),
                        'body' => json_decode($req->getBody(), true),
                    ],
                    'response' => [
                        'status' => $res->getStatusCode(),
                        'headers' => $this->simpleAssoc($res->getHeaders()),
                        'body' => json_decode($res->getBody(), true),
                    ]
                ]);

                // Move pointer to the beginning of the stream
                $res->getBody()->rewind();
            };

            $onRejectedCallback = function (RequestException $e) use ($req, $logger) {
                $logger->error('fail', [
                    'request' => [
                        'uri' => "{$req->getMethod()} {$req->getUri()}",
                        'headers' => $this->simpleAssoc($req->getHeaders()),
                        'body' => json_decode($req->getBody(), true),
                    ],
                    'error' => [
                        'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : $e->getCode(),
                        'message' => $e->getMessage(),
                        'body' => $e->hasResponse() ? json_decode($e->getResponse()->getBody(), true) : null,
                    ]
                ]);
            };

            $promise->then($onFulfilledCallback, $onRejectedCallback);
        };

        $stack = HandlerStack::create();
        $stack->push(Middleware::tap(null,$afterMiddleware));

        return new Client(['handler' => $stack]);
    }

    private function simpleAssoc(array $complexAssoc): array
    {
        return array_map(function ($k, $v) {
            return [$k => $v[0] ?? null];
        }, array_keys($complexAssoc), array_values($complexAssoc));
    }
}