<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase\Mother\Entity;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\Fixtures\Entity\Machine;

final class MachineMother
{
    public static function random(): Machine
    {
        return new Machine(
            Faker::intId(),
            Faker::address(),
            Faker::int(0, 1000),
            Faker::int(0, 1000),
        );
    }
}
