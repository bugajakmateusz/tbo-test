<?php

declare(strict_types=1);

namespace Polsl\Tests\TestCase\Application\Mother;

use Polsl\Domain\Model\Machine\Location;
use Polsl\Domain\Model\Machine\Machine;
use Polsl\Packages\Faker\Faker;
use Polsl\Tests\TestCase\Application\PropertyAccess\PropertyManipulator;

final class MachineMother
{
    public static function random(): Machine
    {
        return self::create();
    }

    private static function create(
        ?string $location = null,
        ?int $positionNumber = null,
        ?int $positionCapacity = null,
    ): Machine {
        $propertyManipulator = PropertyManipulator::getInstance();
        $machine = Machine::create(
            Location::fromString($location ?? Faker::text()),
            $positionNumber ?? Faker::int(),
            $positionCapacity ?? Faker::int(),
        );

        $propertyManipulator->propertySet(
            $machine,
            'id',
            Faker::intId(),
        );

        return $machine;
    }
}
