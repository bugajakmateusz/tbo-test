<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Snack;

use Tab\Domain\DomainException;

class WarehouseSnack
{
    private int $quantity;

    public function __construct(
        private readonly Snack $snack,
    ) {
        $this->quantity = 0;
    }

    public function addQuantity(int $quantity): void
    {
        if (0 >= $quantity) {
            throw new DomainException('Quantity cannot be negative.');
        }

        $this->quantity += $quantity;
    }
}
