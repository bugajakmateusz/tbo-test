<?php

declare(strict_types=1);

namespace Tab\Packages\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tab\Packages\HttpResponse\ResponseInterface;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;

final class SymfonyHttpClient implements ClientInterface
{
    private const OPTION_BODY = 'body';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly JsonSerializerInterface $jsonSerializer,
    ) {
    }

    public function get(string $uri): ResponseInterface
    {
        $response = $this->httpClient
            ->request(
                ClientInterface::METHOD_GET,
                $uri,
            )
        ;

        return new SymfonyResponse($response);
    }

    public function post(string $uri, string $body = ''): ResponseInterface
    {
        $response = $this->httpClient
            ->request(
                ClientInterface::METHOD_POST,
                $uri,
                [self::OPTION_BODY => $body],
            )
        ;

        return new SymfonyResponse($response);
    }

    public function postJson(string $uri, array $body = []): ResponseInterface
    {
        $bodyString = $this->jsonSerializer
            ->encode($body)
        ;
        $response = $this->httpClient
            ->request(
                ClientInterface::METHOD_POST,
                $uri,
                [
                    self::OPTION_BODY => $bodyString,
                    'headers' => [
                        'content-type' => 'application/json',
                    ],
                ],
            )
        ;

        return new SymfonyResponse($response);
    }

    public function request(
        string $method,
        string $uri,
        string $body = '',
        array $headers = [],
        int $timeout = 20,
    ): ResponseInterface {
        $response = $this->httpClient
            ->request(
                $method,
                $uri,
                [
                    self::OPTION_BODY => $body,
                    'headers' => $headers,
                    'timeout' => $timeout,
                ],
            )
        ;

        return new SymfonyResponse($response);
    }
}
