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

    public function decreaseQuantity(int $quantity): void
    {
        if (0 >= $quantity) {
            throw new DomainException('Quantity cannot be negative.');
        }

        $newQuantity = $this->quantity - $quantity;

        if (0 > $newQuantity) {
            throw new DomainException('New quantity cannot be negative.');
        }

        $this->quantity = $newQuantity;
    }
}
