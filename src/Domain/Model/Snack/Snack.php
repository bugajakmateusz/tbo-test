<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Snack;

use Tab\Domain\DomainException;

class Snack
{
    private int $id;

    private readonly ?WarehouseSnack $warehouseSnack;

    private function __construct(
        private string $name,
    ) {
        $this->warehouseSnack = new WarehouseSnack($this);
    }

    public static function create(
        Name $name,
        SnackRepositoryInterface $snackRepository,
    ): self {
        $stringName = $name->toString();
        $existingUser = $snackRepository->findByName($stringName);
        if (null !== $existingUser) {
            throw new DomainException("Snack with name {$stringName} already exists");
        }

        return new self(
            $name->toString(),
        );
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function changeName(
        Name $newName,
    ): void {
        $this->name = $newName->toString();
    }

    public function id(): int
    {
        return $this->id;
    }

    public function addWarehouseQuantity(int $quantity): void
    {
        $this->assertWarehouseSnackIsPresent();
        $this->warehouseSnack
            ->addQuantity($quantity)
        ;
    }

    public function decreaseWarehouseQuantity(int $quantity): void
    {
        $this->assertWarehouseSnackIsPresent();
        $this->warehouseSnack
            ->decreaseQuantity($quantity)
        ;
    }

    /** @phpstan-assert !null $this->warehouseSnack */
    private function assertWarehouseSnackIsPresent(): void
    {
        if (null === $this->warehouseSnack) {
            throw new DomainException('Cannot find warehouse snack');
        }
    }
}
