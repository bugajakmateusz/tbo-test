<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Mother\Entity;

use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\Fixtures\Entity\Snack;
use Tab\Packages\TestCase\Fixtures\Entity\WarehouseSnacks;

final class WarehouseSnackMother
{
    public static function fromSnack(
        Snack $snack,
        ?int $quantity = null,
    ): WarehouseSnacks {
        return new WarehouseSnacks(
            $snack->id,
            $quantity ?? Faker::int(min: 100, max: 10000),
        );
    }
}
