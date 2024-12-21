<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Mother;

use Tab\Domain\Model\Snack\Name;
use Tab\Domain\Model\Snack\Snack;
use Tab\Packages\Faker\Faker;
use Tab\Tests\TestCase\Application\PropertyAccess\PropertyManipulator;

final class SnackMother
{
    public static function random(): Snack
    {
        return self::create();
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

        return $snack;
    }
}
