<?php

declare(strict_types=1);

namespace Tab\Tests\Unit\Domain\Model\Machine;

use Tab\Domain\DomainException;
use Tab\Domain\Model\Machine\MachineSnack;
use Tab\Domain\Model\Machine\SnackPosition;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\UnitTestCase;
use Tab\Tests\TestCase\Application\Mother\MachineMother;
use Tab\Tests\TestCase\Application\Mother\SnackMother;

/**
 * @internal
 */
final class MachineSnackTest extends UnitTestCase
{
    public function test_machine_snack_can_be_created(): void
    {
        // Arrange
        $machine = MachineMother::random();
        $snack = SnackMother::random();
        $position = SnackPosition::fromString(
            Faker::hexBytes(3),
        );

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        MachineSnack::create(
            $machine,
            $snack,
            Faker::int(1, 10),
            $position,
        );
    }

    public function test_machine_snack_cannot_be_created_with_negative_quantity(): void
    {
        // Arrange
        $machine = MachineMother::random();
        $snack = SnackMother::random();
        $position = SnackPosition::fromString(
            Faker::hexBytes(3),
        );

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Quantity cannot be lower than or equal 0.');

        // Act
        MachineSnack::create(
            $machine,
            $snack,
            Faker::int(max: 0),
            $position,
        );
    }
}
