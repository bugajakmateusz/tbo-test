<?php

declare(strict_types=1);

namespace Polsl\Packages\HttpResponse;

interface CookieInterface
{
    public static function fromString(string $cookie): self;

    public function httpOnly(): bool;

    public function name(): string;

    public function value(): string;

    public function toString(): string;

    public function isSecure(): bool;

    public function maxAge(): int;
}
