<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Mother;

use Tab\Domain\Model\Snack\Name;
use Tab\Domain\Model\Snack\Snack;
use Tab\Packages\Faker\Faker;

final class SnackMother
{
    public static function random(): Snack
    {
        return self::create();
    }

    private static function create(
        ?string $name = null,
    ): Snack {
        $nameVO = Name::fromString(
            $name ?? Faker::text(),
        );

        return Snack::create($nameVO);
    }
}
