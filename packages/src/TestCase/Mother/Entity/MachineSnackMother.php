<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Mother\Entity;

use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\Fixtures\Entity\Machine;
use Tab\Packages\TestCase\Fixtures\Entity\MachineSnack;
use Tab\Packages\TestCase\Fixtures\Entity\Snack;

final class MachineSnackMother
{
    public static function fromEntities(
        Machine $machine,
        Snack $snack,
    ): MachineSnack {
        return new MachineSnack(
            Faker::intId(),
            $machine,
            $snack,
            Faker::hexBytes(3),
            Faker::intId(),
        );
    }
}
