<?php

declare(strict_types=1);

namespace Tab\Domain\Service;

interface ClockInterface
{
    public function now(): \DateTimeImmutable;

    public function date(string $datetime): \DateTimeImmutable;
}
