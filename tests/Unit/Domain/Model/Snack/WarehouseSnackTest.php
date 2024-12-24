<?php

declare(strict_types=1);

namespace Tab\Tests\Unit\Domain\Model\Snack;

use Tab\Domain\DomainException;
use Tab\Domain\Model\Snack\WarehouseSnack;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\UnitTestCase;
use Tab\Tests\TestCase\Application\Mother\SnackMother;

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
        $snack = SnackMother::random();
        $warehouseSnack = new WarehouseSnack($snack);

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $warehouseSnack->addQuantity(Faker::int(min: 1));
    }

    public function test_warehouse_snack_quantity_cannot_be_increased_with_non_positive_quantity(): void
    {
        // Arrange
        $snack = SnackMother::random();
        $warehouseSnack = new WarehouseSnack($snack);

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Quantity cannot be negative.');

        // Act
        $warehouseSnack->addQuantity(Faker::int(max: 0));
    }
}
