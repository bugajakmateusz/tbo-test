<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Mother\Entity;

use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\Fixtures\Entity\Snack;

final class SnackMother
{
    public static function random(): Snack
    {
        return new Snack(
            Faker::intId(),
            Faker::name(),
        );
    }
}
