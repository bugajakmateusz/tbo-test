<?php

declare(strict_types=1);

namespace Polsl\Tests\Unit\Domain\Model\Snack;

use Polsl\Domain\DomainException;
use Polsl\Domain\Model\Snack\Buy;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\UnitTestCase;
use Polsl\Tests\TestCase\Application\Mock\FakeClock;
use Polsl\Tests\TestCase\Application\Mother\SnackMother;

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
            Faker::int(1, 100),
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
            Faker::int(1),
        );
    }
}
