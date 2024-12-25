<?php

declare(strict_types=1);

namespace Polsl\Packages\JsonSerializer;

interface JsonSerializerInterface
{
    public function decode(string $jsonString, bool $assoc = false): mixed;

    public function encode(mixed $value, int $options = 0): string;
}
