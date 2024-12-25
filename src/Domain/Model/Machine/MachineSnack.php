<?php

declare(strict_types=1);

namespace Polsl\Domain\Model\Machine;

use Polsl\Domain\DomainException;
use Polsl\Domain\Model\Snack\PriceRepositoryInterface;
use Polsl\Domain\Model\Snack\Snack;
use Polsl\Domain\Model\Snack\SnackSell;
use Polsl\Domain\Model\Snack\SnackSellRepositoryInterface;
use Polsl\Domain\Service\ClockInterface;

class MachineSnack
{
    private int $id;

    private function __construct(
        private Machine $machine,
        private Snack $snack,
        private int $quantity,
        private string $position,
        private \DateTimeImmutable $updatedAt,
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

    public function updateQuantity(
        int $newQuantity,
        ClockInterface $clock,
    ): void {
        if ($this->quantity >= $newQuantity) {
            throw new DomainException('Quantity must be higher than current one.');
        }

        $quantityDiff = $newQuantity - $this->quantity;
        $this->snack->decreaseWarehouseQuantity($quantityDiff);

        $this->quantity = $newQuantity;
        $this->updatedAt = $clock->now();
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
