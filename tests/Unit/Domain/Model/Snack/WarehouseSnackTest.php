<?php

declare(strict_types=1);

namespace Tab\Tests\Unit\Domain\Model\Snack;

use Tab\Domain\DomainException;
use Tab\Domain\Model\Snack\WarehouseSnack;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\UnitTestCase;
use Tab\Tests\TestCase\Application\Mother\SnackMother;
use Tab\Tests\TestCase\Application\Mother\WarehouseSnackMother;

/**
 * @internal
 */
final class WarehouseSnackTest extends UnitTestCase
{
    public function test_warehourse_snack_can_be_created(): void
    {
        // Arrange
        $snack = SnackMother::random();

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        new WarehouseSnack($snack);
    }

    public function test_warehouse_snack_quantity_can_be_increased(): void
    {
        // Arrange
        $warehouseSnack = WarehouseSnackMother::random();

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $warehouseSnack->addQuantity(Faker::int(min: 1));
    }

    public function test_warehouse_snack_quantity_cannot_be_increased_with_non_positive_quantity(): void
    {
        // Arrange
        $warehouseSnack = WarehouseSnackMother::random();

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Quantity cannot be negative.');

        // Act
        $warehouseSnack->addQuantity(Faker::int(max: 0));
    }

    public function test_warehouse_snack_quantity_can_be_decreased(): void
    {
        // Arrange
        $quantity = Faker::int(0, 200);
        $warehouseSnack = WarehouseSnackMother::withQuantity($quantity);

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $warehouseSnack->decreaseQuantity(
            Faker::int(1, $quantity),
        );
    }

    public function test_warehouse_snack_quantity_cannot_be_decreased_with_non_positive_quantity(): void
    {
        // Arrange
        $warehouseSnack = WarehouseSnackMother::random();

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Quantity cannot be negative.');

        // Act
        $warehouseSnack->decreaseQuantity(Faker::int(max: 0));
    }

    public function test_warehouse_snack_quantity_cannot_be_decreased_below_zero(): void
    {
        // Arrange
        $quantity = Faker::int(0, 200);
        $warehouseSnack = WarehouseSnackMother::withQuantity($quantity);

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage('New quantity cannot be negative.');

        // Act
        $warehouseSnack->decreaseQuantity(
            Faker::int($quantity),
        );
    }
}
