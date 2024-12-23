<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Mock;

use Tab\Domain\Service\ClockInterface;

final class FakeClock implements ClockInterface
{
    private static ?FakeClock $clock = null;

    public function __construct(private readonly ?\DateTimeImmutable $now = null) {}

    public function now(): \DateTimeImmutable
    {
        return $this->now ?? new \DateTimeImmutable();
    }

    public function date(string $datetime): \DateTimeImmutable
    {
        return new \DateTimeImmutable($datetime);
    }

    public static function getInstance(): self
    {
        return self::$clock ??= new self();
    }
}
