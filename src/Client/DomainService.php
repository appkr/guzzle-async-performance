<?php

namespace Client;

use Common\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
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
            $response = $this->getLoggableClient('sync')->send($request);
        } catch (\Exception $e) {
            throw $e;
        }

        // Simulate costly job
        usleep(10000); // 10ms

        return json_decode($response->getBody()->getContents(), true);
    }

    public function async(): ?array
    {
        $request = new Request('GET', getenv("SERVER_URI"), [
            "accept" => "application/json",
        ]);
        $promise = $this->getLoggableClient('async')->sendAsync($request)
            ->then(function (ResponseInterface $response) use (&$promise) {
                $content = $response->getBody()->getContents();
                return json_decode($content, true);
            }, function (RequestException $e) {
                throw $e;
            });

        // Simulate costly job
        usleep(10000); // 10ms

        return $promise->wait();
    }

    private function getLoggableClient(string $context): Client
    {
        $logger = Logger::create("client.RequestService.{$context}", __DIR__.'/../../logs/client.log');

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