<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Mother\Entity;

use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\Fixtures\Entity\Machine;
use Tab\Packages\TestCase\Fixtures\Entity\Snack;
use Tab\Packages\TestCase\Fixtures\Entity\SnackPrice;

final class SnackPriceMother
{
    public static function fromEntities(
        Machine $machine,
        Snack $snack,
        ?float $price = null,
        ?\DateTimeImmutable $createdAt = null,
    ): SnackPrice {
        return new SnackPrice(
            Faker::intId(),
            $machine,
            $snack,
            $price ?? Faker::float(2, max: 1000.0),
            $createdAt ?? Faker::now(),
        );
    }
}
