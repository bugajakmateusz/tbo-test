<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Mother;

use Tab\Domain\Model\Machine\Location;
use Tab\Domain\Model\Machine\Machine;
use Tab\Packages\Faker\Faker;

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
        return Machine::create(
            Location::fromString($location ?? Faker::text()),
            $positionNumber ?? Faker::int(),
            $positionCapacity ?? Faker::int(),
        );
    }
}
