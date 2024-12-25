<?php

declare(strict_types=1);

namespace Polsl\Tests\TestCase\Application\Mother;

use Polsl\Domain\Model\Snack\WarehouseSnack;
use Polsl\Tests\TestCase\Application\PropertyAccess\PropertyManipulator;

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
