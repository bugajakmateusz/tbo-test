<?php

declare(strict_types=1);

namespace Polsl\Tests\TestCase\Application\Mother;

use Polsl\Domain\Model\Snack\Name;
use Polsl\Domain\Model\Snack\Snack;
use Polsl\Packages\Faker\Faker;
use Polsl\Tests\TestCase\Application\PropertyAccess\PropertyManipulator;

final class SnackMother
{
    /** @var null|\ReflectionClass<Snack> */
    private static ?\ReflectionClass $snackReflection = null;

    public static function random(): Snack
    {
        return self::create();
    }

    public static function createWithoutWarehouseSnack(): Snack
    {
        self::$snackReflection ??= new \ReflectionClass(Snack::class);
        /** @var Snack $snack */
        $snack = self::$snackReflection->newInstanceWithoutConstructor();
        $properties = [
            'id' => Faker::intId(),
            'name' => Faker::words(3),
            'warehouseSnack' => null,
        ];

        PropertyManipulator::getInstance()->propertiesSet(
            $snack,
            $properties,
        );

        return $snack;
    }

    private static function create(
        ?string $name = null,
    ): Snack {
        $propertyManipulator = PropertyManipulator::getInstance();
        $nameVO = Name::fromString(
            $name ?? Faker::text(),
        );

        $snack = Snack::create($nameVO);
        $propertyManipulator->propertySet(
            $snack,
            'id',
            Faker::intId(),
        );

        $snack->addWarehouseQuantity(Faker::int(100, 1000));

        return $snack;
    }
}
