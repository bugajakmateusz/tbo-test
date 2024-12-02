<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase;

use PHPUnit\Framework\TestCase;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;
use Tab\Packages\JsonSerializer\NativeJsonSerializer;

abstract class UnitTestCase extends TestCase
{
    private static ?JsonSerializerInterface $jsonSerializer = null;

    protected function jsonDecode(string $json, bool $array = true): mixed
    {
        $jsonSerializer = $this->jsonSerializer();

        return $jsonSerializer->decode($json, $array);
    }

    protected function jsonEncode(mixed $value): string
    {
        $jsonSerializer = $this->jsonSerializer();

        return $jsonSerializer->encode($value);
    }

    protected function jsonSerializer(): JsonSerializerInterface
    {
        return self::$jsonSerializer ??= new NativeJsonSerializer();
    }
}
