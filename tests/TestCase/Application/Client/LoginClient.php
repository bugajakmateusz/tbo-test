<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Client;

use http\Exception\RuntimeException;
use Tab\Packages\HttpResponse\ResponseInterface;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;
use Tab\Packages\TestCase\Client\KernelBrowserInterface;
use Tab\Packages\TestCase\Client\RequestInterface;

final class LoginClient implements ClientInterface
{
    public function __construct(
        private readonly KernelBrowserInterface $httpClient,
        private readonly JsonSerializerInterface $jsonSerializer,
    ) {}

    public function login(string $userName, string $password): ResponseInterface
    {
        $this->httpClient
            ->followRedirects(false)
        ;
        $content = $this->jsonSerializer
            ->encode(
                [
                    'username' => $userName,
                    'password' => $password,
                ],
            )
        ;

        return $this->httpClient
            ->request(
                RequestInterface::METHOD_POST,
                '/api/login',
                [],
                $content,
                ['CONTENT_TYPE' => 'application/json'],
            )
        ;
    }

    public function withHttpClient(KernelBrowserInterface $httpClient): ClientInterface
    {
        throw new RuntimeException('Not implemented.');
    }
}
