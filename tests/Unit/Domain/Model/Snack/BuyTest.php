<?php

declare(strict_types=1);

namespace Tab\Tests\Unit\Domain\Model\Snack;

use Tab\Domain\DomainException;
use Tab\Domain\Model\Snack\Buy;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\UnitTestCase;
use Tab\Tests\TestCase\Application\Mock\FakeClock;
use Tab\Tests\TestCase\Application\Mother\SnackMother;

/**
 * @internal
 */
final class BuyTest extends UnitTestCase
{
    public function test_buy_can_be_created(): void
    {
        // Arrange
        $snack = SnackMother::random();

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        Buy::create(
            $snack,
            FakeClock::getInstance(),
            Faker::float(min: 0.1),
        );
    }

    public function test_buy_cannot_be_created_with_non_positive_value(): void
    {
        // Arrange
        $snack = SnackMother::random();

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Price cannot be lower than or equal 0.');

        // Act
        Buy::create(
            $snack,
            FakeClock::getInstance(),
            Faker::float(min: -100, max: 0.0),
        );
    }
}
