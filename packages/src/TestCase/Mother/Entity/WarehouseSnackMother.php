<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase\Mother\Entity;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\Fixtures\Entity\Snack;
use Polsl\Packages\TestCase\Fixtures\Entity\WarehouseSnacks;

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
