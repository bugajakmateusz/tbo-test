<?php

declare(strict_types=1);

namespace Tab\Packages\HttpClient;

use Tab\Packages\HttpResponse\ResponseInterface;

interface ClientInterface
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';

    public function get(string $uri): ResponseInterface;

    public function post(string $uri, string $body = ''): ResponseInterface;

    /** @param array<mixed,mixed> $body */
    public function postJson(string $uri, array $body = []): ResponseInterface;

    /** @param array<string,array<string>|string> $headers */
    public function request(
        string $method,
        string $uri,
        string $body = '',
        array $headers = [],
        int $timeout = 20,
    ): ResponseInterface;
}
