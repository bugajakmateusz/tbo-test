<?php

declare(strict_types=1);

namespace Tab\Packages\HttpResponse;

use Symfony\Component\HttpFoundation\Cookie;

final readonly class SymfonyCookie implements CookieInterface
{
    public function __construct(private Cookie $cookie) {}

    public static function fromString(string $cookie): CookieInterface
    {
        return new self(Cookie::fromString($cookie));
    }

    public function httpOnly(): bool
    {
        return $this->cookie
            ->isHttpOnly()
        ;
    }

    public function name(): string
    {
        return $this->cookie
            ->getName()
        ;
    }

    public function value(): string
    {
        return $this->cookie
            ->getValue() ?? ''
        ;
    }

    public function isSecure(): bool
    {
        return $this->cookie
            ->isSecure()
        ;
    }

    public function toString(): string
    {
        return (string) $this->cookie;
    }

    public function maxAge(): int
    {
        return $this->cookie
            ->getMaxAge()
        ;
    }
}
