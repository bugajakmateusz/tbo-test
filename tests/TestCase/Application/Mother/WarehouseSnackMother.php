<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Mother;

use Tab\Domain\Model\Snack\WarehouseSnack;
use Tab\Tests\TestCase\Application\PropertyAccess\PropertyManipulator;

final class WarehouseSnackMother
{
    public static function random(): WarehouseSnack
    {
        return self::create();
    }

    public static function withQuantity(int $quantity): WarehouseSnack
    {
        $propertyManipulator = PropertyManipulator::getInstance();
        $warehouseSnack = self::create();
        $propertyManipulator->propertySet(
            $warehouseSnack,
            'quantity',
            $quantity,
        );

        return $warehouseSnack;
    }

    private static function create(): WarehouseSnack
    {
        $snack = SnackMother::random();

        return new WarehouseSnack($snack);
    }
}
