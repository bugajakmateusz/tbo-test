<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Snack;

use Tab\Domain\DomainException;
use Tab\Domain\Service\ClockInterface;

class Buy
{
    private int $id;

    private function __construct(
        private int $snackId,
        private \DateTimeImmutable $date,
        private float $price,
        private int $quantity,
    ) {}

    public static function create(
        Snack $snack,
        ClockInterface $clock,
        float $price,
        int $quantity,
    ): self {
        if ($price <= 0) {
            throw new DomainException('Price cannot be lower than or equal 0.');
        }

        if ($quantity <= 0) {
            throw new DomainException('Quantity cannot be lower than or equal 0.');
        }

        return new self(
            $snack->id(),
            $clock->now(),
            $price,
            $quantity,
        );
    }
}
