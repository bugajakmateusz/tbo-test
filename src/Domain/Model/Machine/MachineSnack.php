<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Machine;

use Tab\Domain\DomainException;
use Tab\Domain\Model\Snack\Snack;

class MachineSnack
{
    private int $id;

    private function __construct(
        private Machine $machine,
        private Snack $snack,
        private int $quantity,
        private string $position,
    ) {}

    public static function create(
        Machine $machine,
        Snack $snack,
        int $quantity,
        SnackPosition $position,
    ): self {
        if ($quantity <= 0) {
            throw new DomainException('Quantity cannot be lower than or equal 0.');
        }

        $snack->decreaseWarehouseQuantity($quantity);

        return new self(
            $machine,
            $snack,
            $quantity,
            $position->toString(),
        );
    }

    public function position(): string
    {
        return $this->position;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}
