<?php

declare(strict_types=1);

namespace Tab\Packages\HttpClient;

use Tab\Packages\HttpResponse\ResponseInterface;

final class SymfonyResponse implements ResponseInterface
{
    public function __construct(private readonly \Symfony\Contracts\HttpClient\ResponseInterface $innerResponse)
    {
    }

    public function statusCode(): int
    {
        return $this->innerResponse
            ->getStatusCode()
        ;
    }

    public function isOk(): bool
    {
        return ResponseInterface::HTTP_OK === $this->statusCode();
    }

    public function uri(): string
    {
        throw new \RuntimeException('Not implemented.');
    }

    public function content(): string
    {
        return $this->innerResponse
            ->getContent()
        ;
    }

    public function contentType(): string
    {
        throw new \RuntimeException('Not implemented.');
    }

    public function headers(): array
    {
        return $this->innerResponse
            ->getHeaders()
        ;
    }

    public function location(): string
    {
        throw new \RuntimeException('Not implemented.');
    }

    public function isRedirection(): bool
    {
        throw new \RuntimeException('Not implemented.');
    }

    public function cookies(): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    public function filePath(): string
    {
        throw new \RuntimeException('Not implemented.');
    }
}
