<?php

declare(strict_types=1);

namespace Tab\Tests\Unit\Domain\Model\Machine;

use Tab\Domain\Model\Machine\Location;
use Tab\Domain\Model\Machine\Machine;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\UnitTestCase;
use Tab\Tests\TestCase\Application\Mother\MachineMother;

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
