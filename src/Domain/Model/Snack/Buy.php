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
    ) {}

    public static function create(
        Snack $snack,
        ClockInterface $clock,
        float $price,
    ): self {
        if ($price <= 0) {
            throw new DomainException('Price cannot be lower than or equal 0.');
        }

        return new self(
            $snack->id(),
            $clock->now(),
            $price,
        );
    }
}
