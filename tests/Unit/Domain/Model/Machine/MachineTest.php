<?php

declare(strict_types=1);

namespace Polsl\Tests\Unit\Domain\Model\Machine;

use Polsl\Domain\DomainException;
use Polsl\Domain\Model\Machine\Location;
use Polsl\Domain\Model\Machine\Machine;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\UnitTestCase;
use Polsl\Tests\TestCase\Application\Mother\MachineMother;
use Polsl\Tests\TestCase\Application\Mother\MachineSnackMother;

/**
 * @internal
 */
final class MachineTest extends UnitTestCase
{
    public function test_machine_can_be_created(): void
    {
        // Arrange
        $location = Location::fromString(
            Faker::text(),
        );

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        Machine::create(
            $location,
            Faker::int(),
            Faker::int(),
        );
    }

    /**
     * @param \Closure(): array{
     *     method: string,
     *     value: Location|int,
     * } $createParams
     *
     * @dataProvider propertiesDataProvider
     */
    public function test_properties_can_be_changed(\Closure $createParams): void
    {
        // Arrange
        [
            'method' => $method,
            'value' => $value,
        ] = $createParams();
        $machine = MachineMother::random();

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $machine->{$method}($value);
    }

    public function test_machine_snack_can_be_added(): void
    {
        $machineSnack = MachineSnackMother::random();
        $machine = MachineMother::random();

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $machine->addSnack($machineSnack);
    }

    public function test_machine_snack_can_be_added_on_empty_position(): void
    {
        $position = Faker::hexBytes(3);
        $emptyMachineSnack = MachineSnackMother::withQuantity(0, $position);
        $machineSnack = MachineSnackMother::withPosition($position);
        $machine = MachineMother::random();
        $machine->addSnack($emptyMachineSnack);

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $machine->addSnack($machineSnack);
    }

    public function test_machine_snack_cannot_be_added_when_position_is_taken(): void
    {
        // Arrange
        $position = Faker::hexBytes(3);
        $machineSnack1 = MachineSnackMother::withPosition($position);
        $machineSnack2 = MachineSnackMother::withPosition($position);
        $machine = MachineMother::random();
        $machine->addSnack($machineSnack1);

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Snack on provided position already placed');

        // Act
        $machine->addSnack($machineSnack2);
    }

    /** @return iterable<string, array{\Closure}> */
    public static function propertiesDataProvider(): iterable
    {
        yield 'location' => [
            static fn (): array => [
                'method' => 'changeLocation',
                'value' => Location::fromString(Faker::word()),
            ],
        ];

        yield 'position number' => [
            static fn (): array => [
                'method' => 'changePositionNumber',
                'value' => Faker::int(),
            ],
        ];

        yield 'position capacity' => [
            static fn (): array => [
                'method' => 'changePositionCapacity',
                'value' => Faker::int(),
            ],
        ];
    }
}
