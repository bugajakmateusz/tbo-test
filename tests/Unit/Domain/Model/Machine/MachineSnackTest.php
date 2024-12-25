<?php

declare(strict_types=1);

namespace Polsl\Tests\Unit\Domain\Model\Machine;

use Polsl\Domain\DomainException;
use Polsl\Domain\Model\Machine\MachineSnack;
use Polsl\Domain\Model\Machine\SnackPosition;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\UnitTestCase;
use Polsl\Tests\TestCase\Application\Mock\FakeClock;
use Polsl\Tests\TestCase\Application\Mother\MachineMother;
use Polsl\Tests\TestCase\Application\Mother\MachineSnackMother;
use Polsl\Tests\TestCase\Application\Mother\SnackMother;

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
            new FakeClock(),
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
            new FakeClock(),
        );
    }

    public function test_snack_quantity_can_be_increased(): void
    {
        // Arrange
        $quantity = Faker::int(2, 10);
        $machineSnack = MachineSnackMother::withQuantity($quantity);

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $machineSnack->updateQuantity(
            $quantity + 1,
            new FakeClock(),
        );
    }

    /**
     * @param \Closure(): array{
     *     machineSnack: MachineSnack,
     *     quantity: int,
     * } $createParams
     *
     * @dataProvider quantityDataProvider
     */
    public function test_cannot_modify_machine_snack_quantity_when_new_quantity_is_not_higher(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'machineSnack' => $machineSnack,
            'quantity' => $quantity,
        ] = $createParams();

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Quantity must be higher than current one.');

        // Act
        $machineSnack->updateQuantity(
            $quantity,
            new FakeClock(),
        );
    }

    /** @return iterable<string, array{\Closure}> */
    public static function quantityDataProvider(): iterable
    {
        yield 'same quantity' => [
            static function (): array {
                $quantity = Faker::intId();

                return [
                    'machineSnack' => MachineSnackMother::withQuantity($quantity),
                    'quantity' => $quantity,
                ];
            },
        ];

        yield 'lower quantity' => [
            static function (): array {
                $quantity = Faker::intId();

                return [
                    'machineSnack' => MachineSnackMother::withQuantity($quantity),
                    'quantity' => $quantity - 1,
                ];
            },
        ];
    }
}
