<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Machine;

use Tab\Domain\DomainException;
use Tab\Domain\Model\Snack\Snack;
use Tab\Domain\Service\ClockInterface;

class MachineSnack
{
    private int $id;

    private function __construct(
        private Machine $machine,
        private Snack $snack,
        private int $quantity,
        private string $position,
        private \DateTimeImmutable $last_updated_at,
    ) {}

    public static function create(
        Machine $machine,
        Snack $snack,
        int $quantity,
        SnackPosition $position,
        ClockInterface $clock,
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
            $clock->now(),
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

    public function updateQuantity(int $newQuantity, ClockInterface $clock): void
    {
        if ($this->quantity >= $newQuantity) {
            throw new DomainException('Quantity must be higher than current one.');
        }

        $quantityDiff = $newQuantity - $this->quantity;
        $this->snack->decreaseWarehouseQuantity($quantityDiff);

        $this->quantity = $newQuantity;
        $this->last_updated_at = $clock->now();
    }
}
