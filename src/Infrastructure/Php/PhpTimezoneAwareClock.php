<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Php;

use Tab\Domain\Service\ClockInterface;

final class PhpTimezoneAwareClock implements ClockInterface
{
    private readonly \DateTimeZone $timezone;

    public function __construct(string $timezone)
    {
        $this->timezone = new \DateTimeZone($timezone);
    }

    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable(timezone: $this->timezone);
    }

    public function date(string $datetime): \DateTimeImmutable
    {
        return new \DateTimeImmutable($datetime, $this->timezone);
    }
}
