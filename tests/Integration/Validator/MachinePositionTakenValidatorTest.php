<?php

declare(strict_types=1);

namespace Integration\Validator;

use Tab\Application\Command\AddNewMachineSnack\AddNewMachineSnack;
use Tab\Infrastructure\Symfony\Validator\Constraints\MachinePositionTaken;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\IntegrationTestCase;
use Tab\Packages\TestCase\Mother\Entity\MachineMother;
use Tab\Packages\TestCase\Mother\Entity\MachineSnackMother;
use Tab\Packages\TestCase\Mother\Entity\SnackMother;
use Tab\Packages\TestCase\Validator\TestValidator;

/** @internal */
final class MachinePositionTakenValidatorTest extends IntegrationTestCase
{
    /** @dataProvider positionDataProvider */
    public function test_validate_if_position_is_taken(\Closure $createParams): void
    {
        // Arrange
        /**
         * @var object[] $entities
         * @var int      $machineId
         * @var string   $position
         * @var int      $errorCount
         */
        [
            'entities' => $entities,
            'machineId' => $machineId,
            'position' => $position,
            'errorCount' => $errorCount,
        ] = $createParams();
        /** @var TestValidator $validator */
        $validator = $this->service(TestValidator::class);
        $this->loadEntities(...$entities);
        $command = new AddNewMachineSnack(
            $machineId,
            Faker::intId(),
            Faker::intId(),
            $position,
            Faker::float(min: 1.0),
        );

        // Act
        $errors = $validator->validate(
            $command,
            new MachinePositionTaken(),
        );

        // Assert
        self::assertCount($errorCount, $errors->toArray());
    }

    /** @return iterable<string,array{\Closure}> */
    public static function positionDataProvider(): iterable
    {
        yield 'previously taken position, now empty' => [
            static function (): array {
                $machine = MachineMother::random();
                $snack = SnackMother::random();
                $position = Faker::hexBytes(3);

                return [
                    'entities' => [
                        $machine,
                        $snack,
                        MachineSnackMother::fromEntities(
                            $machine,
                            $snack,
                            $position,
                            0,
                        ),
                    ],
                    'machineId' => $machine->id,
                    'position' => $position,
                    'errorCount' => 0,
                ];
            },
        ];

        yield 'previously taken position, not empty' => [
            static function (): array {
                $machine = MachineMother::random();
                $snack = SnackMother::random();
                $position = Faker::hexBytes(3);

                return [
                    'entities' => [
                        $machine,
                        $snack,
                        MachineSnackMother::fromEntities(
                            $machine,
                            $snack,
                            $position,
                        ),
                    ],
                    'machineId' => $machine->id,
                    'position' => $position,
                    'errorCount' => 1,
                ];
            },
        ];

        yield 'not taken position' => [
            static fn (): array => [
                'entities' => [],
                'machineId' => Faker::intId(),
                'position' => Faker::hexBytes(3),
                'errorCount' => 0,
            ],
        ];
    }
}
