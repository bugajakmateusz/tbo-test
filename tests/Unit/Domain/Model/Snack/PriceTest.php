<?php

declare(strict_types=1);

namespace Tab\Tests\Unit\Domain\Model\Snack;

use Tab\Domain\DomainException;
use Tab\Domain\Model\Snack\Price;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\UnitTestCase;
use Tab\Tests\TestCase\Application\Mock\FakeClock;
use Tab\Tests\TestCase\Application\Mother\MachineMother;
use Tab\Tests\TestCase\Application\Mother\SnackMother;

/**
 * @internal
 */
final class PriceTest extends UnitTestCase
{
    public function test_price_can_be_created(): void
    {
        // Arrange
        $machine = MachineMother::random();
        $snack = SnackMother::random();

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        Price::create(
            $machine,
            $snack,
            Faker::float(min: 0.1),
            FakeClock::getInstance(),
        );
    }

    public function test_price_cannot_be_created_with_non_positive_value(): void
    {
        // Arrange
        $machine = MachineMother::random();
        $snack = SnackMother::random();

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Price cannot be lower than or equal 0.');

        // Act
        Price::create(
            $machine,
            $snack,
            Faker::float(min: -100, max: 0.0),
            FakeClock::getInstance(),
        );
    }
}
