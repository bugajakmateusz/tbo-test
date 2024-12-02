<?php

declare(strict_types=1);

namespace Tab\Packages\HttpResponse;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

final readonly class SymfonyResponse implements ResponseInterface
{
    public function __construct(
        private Response $response,
        private string $uri,
    ) {
    }

    public function statusCode(): int
    {
        return $this->response
            ->getStatusCode()
        ;
    }

    public function isOk(): bool
    {
        return $this->response
            ->isOk()
        ;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function content(): string
    {
        return (string) $this->response
            ->getContent()
        ;
    }

    public function contentType(): string
    {
        $headers = $this->response
            ->headers;

        return $headers->get(ResponseInterface::HEADER_CONTENT_TYPE, '');
    }

    public function location(): string
    {
        $headers = $this->response
            ->headers;

        return $headers->get(ResponseInterface::HEADER_LOCATION, '');
    }

    public function cookies(): array
    {
        $headers = $this->response
            ->headers;

        return \array_map(
            static fn (Cookie $cookie): CookieInterface => new SymfonyCookie($cookie),
            $headers->getCookies(),
        );
    }

    public function headers(): array
    {
        $headers = $this->response
            ->headers
        ;

        return $headers->all();
    }

    public function isRedirection(): bool
    {
        return $this->response
            ->isRedirection()
        ;
    }

    public function filePath(): string
    {
        if (!$this->response instanceof BinaryFileResponse) {
            throw new \RuntimeException('Unable to get file path from non-binary-file response.');
        }

        $file = $this->response
            ->getFile()
        ;

        return $file->getPathname();
    }
}
