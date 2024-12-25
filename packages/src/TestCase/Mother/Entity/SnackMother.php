<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase\Mother\Entity;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\Fixtures\Entity\Snack;

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
