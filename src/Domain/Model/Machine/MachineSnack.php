<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Machine;

use Tab\Domain\DomainException;
use Tab\Domain\Model\Snack\PriceRepositoryInterface;
use Tab\Domain\Model\Snack\Snack;
use Tab\Domain\Model\Snack\SnackSell;
use Tab\Domain\Model\Snack\SnackSellRepositoryInterface;
use Tab\Domain\Service\ClockInterface;

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

    public function updateQuantity(int $newQuantity): void
    {
        if ($this->quantity >= $newQuantity) {
            throw new DomainException('Quantity must be higher than current one.');
        }

        $quantityDiff = $newQuantity - $this->quantity;
        $this->snack->decreaseWarehouseQuantity($quantityDiff);

        $this->quantity = $newQuantity;
    }

    public function sellSnack(
        PriceRepositoryInterface $priceRepository,
        ClockInterface $clock,
        SnackSellRepositoryInterface $snackSellRepository,
    ): void {
        $newQuantity = $this->quantity - 1;
        if ($newQuantity < 0) {
            throw new DomainException('New quantity cannot be negative');
        }
        $price = $priceRepository
            ->getActualPrice($this->machine, $this->snack)
        ;
        $sell = SnackSell::create(
            $price,
            $clock,
        );
        $snackSellRepository->add($sell);
        $this->quantity = $newQuantity;
    }
}
