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
        ?string $position = null,
        ?int $quantity = null,
    ): MachineSnack {
        return new MachineSnack(
            Faker::intId(),
            $machine,
            $snack,
            $position ?? Faker::hexBytes(3),
            $quantity ?? Faker::intId(),
        );
    }
}
