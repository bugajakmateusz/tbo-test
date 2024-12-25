<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase;

use PHPUnit\Framework\TestCase;
use Polsl\Packages\JsonSerializer\JsonSerializerInterface;
use Polsl\Packages\JsonSerializer\NativeJsonSerializer;

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
