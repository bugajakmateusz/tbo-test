<?php

declare(strict_types=1);

namespace Polsl\Domain\Model\Snack;

use Polsl\Domain\Service\ClockInterface;

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
