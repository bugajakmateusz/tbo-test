<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Mother;

use Tab\Domain\Model\Snack\Name;
use Tab\Domain\Model\Snack\Snack;
use Tab\Packages\Faker\Faker;
use Tab\Tests\TestCase\Application\Mock\FakeSnackRepository;
use Tab\Tests\TestCase\Application\PropertyAccess\PropertyManipulator;

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
        $snackRepository = new FakeSnackRepository();
        $propertyManipulator = PropertyManipulator::getInstance();
        $nameVO = Name::fromString(
            $name ?? Faker::text(),
        );

        $snack = Snack::create($nameVO, $snackRepository);
        $propertyManipulator->propertySet(
            $snack,
            'id',
            Faker::intId(),
        );

        $snack->addWarehouseQuantity(Faker::int(100, 1000));

        return $snack;
    }
}
