<?php

declare(strict_types=1);

namespace Tab\Packages\Responder\Response;

use Tab\Packages\HttpResponse\CookieInterface;

final class ResponseSpecification
{
    /** @var CookieInterface[] */
    private array $cookies = [];
    private ?string $contentType = null;
    private ?int $sharedMaxAge = null;
    private ?int $maxAge = null;
    /** @var array<string,string[]> */
    private array $headers = [];

    public function withCookies(CookieInterface ...$cookies): self
    {
        $clone = clone $this;
        $clone->cookies = $cookies;

        return $clone;
    }

    /** @return CookieInterface[] */
    public function cookies(): array
    {
        return $this->cookies;
    }

    public function withContentType(string $contentType): self
    {
        $clone = clone $this;
        $clone->contentType = $contentType;

        return $clone;
    }

    public function contentType(): ?string
    {
        return $this->contentType;
    }

    public function withSharedMaxAge(int $sharedMaxAge): self
    {
        $clone = clone $this;
        $clone->sharedMaxAge = $sharedMaxAge;

        return $clone;
    }

    public function sharedMaxAge(): ?int
    {
        return $this->sharedMaxAge;
    }

    public function withMaxAge(int $maxAge): self
    {
        $clone = clone $this;
        $clone->maxAge = $maxAge;

        return $clone;
    }

    public function maxAge(): ?int
    {
        return $this->maxAge;
    }

    public function withHeader(string $name, string ...$value): self
    {
        $headerName = \strtolower(
            \trim(
                $name,
            ),
        );
        $clone = clone $this;
        $clone->headers[$headerName] = $value;

        return $clone;
    }

    /** @return array<string,string[]> */
    public function headers(): array
    {
        return $this->headers;
    }
}
