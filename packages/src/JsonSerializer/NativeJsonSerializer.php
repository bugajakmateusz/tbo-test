<?php

declare(strict_types=1);

namespace Tab\Packages\JsonSerializer;

final class NativeJsonSerializer implements JsonSerializerInterface
{
    public function decode(string $jsonString, bool $assoc = false): mixed
    {
        return \json_decode(
            $jsonString,
            $assoc,
            flags: \JSON_THROW_ON_ERROR,
        );
    }

    public function encode(mixed $value, int $options = 0): string
    {
        return \json_encode($value, $options | \JSON_THROW_ON_ERROR);
    }
}
