<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Snack;

use Tab\Domain\Service\ClockInterface;

class SnackSell
{
    private int $id;

    private function __construct(
        private int $priceId,
        private \DateTimeImmutable $soldAt,
    ) {}

    public static function create(
        Price $price,
        ClockInterface $clock,
    ): self {
        return new self(
            $price->id(),
            $clock->now(),
        );
    }
}
