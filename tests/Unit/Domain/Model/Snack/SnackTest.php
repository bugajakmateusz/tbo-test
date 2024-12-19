<?php

declare(strict_types=1);

namespace Tab\Tests\Unit\Domain\Model\Snack;

use Tab\Domain\Model\Snack\Name;
use Tab\Domain\Model\Snack\Snack;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\UnitTestCase;
use Tab\Tests\TestCase\Application\Mother\SnackMother;

/**
 * @internal
 */
final class SnackTest extends UnitTestCase
{
    public function test_snack_can_be_created(): void
    {
        // Arrange
        $name = Name::fromString(Faker::text());

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        Snack::create($name);
    }

    public function test_name_can_be_changed(): void
    {
        // Arrange
        $snack = SnackMother::random();
        $name = Name::fromString(
            Faker::text(),
        );

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $snack->changeName($name);
    }
}
