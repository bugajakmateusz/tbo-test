<?php

declare(strict_types=1);

namespace Polsl\Tests\Unit\Domain\Model\Snack;

use Polsl\Domain\DomainException;
use Polsl\Domain\Model\Snack\Price;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\UnitTestCase;
use Polsl\Tests\TestCase\Application\Mock\FakeClock;
use Polsl\Tests\TestCase\Application\Mother\MachineMother;
use Polsl\Tests\TestCase\Application\Mother\SnackMother;

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
