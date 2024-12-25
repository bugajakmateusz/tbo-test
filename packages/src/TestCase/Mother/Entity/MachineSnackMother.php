<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase\Mother\Entity;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\Fixtures\Entity\Machine;
use Polsl\Packages\TestCase\Fixtures\Entity\MachineSnack;
use Polsl\Packages\TestCase\Fixtures\Entity\Snack;

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
