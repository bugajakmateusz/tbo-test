<?php

declare(strict_types=1);

namespace Tab\Packages\Constants;

final class Hasher extends Constants
{
    public const SODIUM_TIME_COST = 8;
    public const SODIUM_MEMORY_LIMIT_IN_MB = 8;
    public const SODIUM_MEMORY_LIMIT_IN_KB = self::SODIUM_MEMORY_LIMIT_IN_MB * 1024;
    public const SODIUM_MEMORY_LIMIT_IN_BYTES = self::SODIUM_MEMORY_LIMIT_IN_KB * 1024;

    public const TEST_SODIUM_TIME_COST = 3;
    public const TEST_SODIUM_MEMORY_LIMIT_IN_KB = 10;
    public const TEST_SODIUM_MEMORY_LIMIT_IN_BYTES = self::TEST_SODIUM_MEMORY_LIMIT_IN_KB * 1024;
}
