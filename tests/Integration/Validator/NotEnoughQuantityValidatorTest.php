<?php

declare(strict_types=1);

namespace Integration\Validator;

use Tab\Application\Command\AddNewMachineSnack\AddNewMachineSnack;
use Tab\Application\Command\UpdateMachineSnack\UpdateMachineSnack;
use Tab\Infrastructure\Symfony\Validator\Constraints\NotEnoughQuantity;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\Fixtures\Entity\Machine;
use Tab\Packages\TestCase\Fixtures\Entity\Snack;
use Tab\Packages\TestCase\IntegrationTestCase;
use Tab\Packages\TestCase\Mother\Entity\MachineMother;
use Tab\Packages\TestCase\Mother\Entity\MachineSnackMother;
use Tab\Packages\TestCase\Mother\Entity\SnackMother;
use Tab\Packages\TestCase\Mother\Entity\WarehouseSnackMother;
use Tab\Packages\TestCase\Validator\TestValidator;

/** @internal */
final class NotEnoughQuantityValidatorTest extends IntegrationTestCase
{
    /** @dataProvider quantityDataProvider */
    public function test_validate_quantity_add(\Closure $createParams): void
    {
        // Arrange
        /**
         * @var Snack    $snack
         * @var Machine  $machine
         * @var object[] $entities
         * @var object[] $entities
         * @var int      $quantity
         * @var int      $errorCount
         */
        [
            'snack' => $snack,
            'machine' => $machine,
            'entities' => $entities,
            'quantity' => $quantity,
            'errorCount' => $errorCount,
        ] = $createParams();
        /** @var TestValidator $validator */
        $validator = $this->service(TestValidator::class);
        $this->loadEntities(
            $machine,
            $snack,
            ...$entities,
        );
        $command = new AddNewMachineSnack(
            Faker::intId(),
            $snack->id,
            $quantity,
            Faker::hexBytes(3),
        );

        // Act
        $errors = $validator->validate(
            $command,
            new NotEnoughQuantity(),
        );

        // Assert
        self::assertCount($errorCount, $errors->toArray());
    }

    /** @dataProvider quantityDataProvider */
    public function test_validate_quantity_update(\Closure $createParams): void
    {
        // Arrange
        /**
         * @var Snack    $snack
         * @var Machine  $machine
         * @var object[] $entities
         * @var object[] $entities
         * @var int      $quantity
         * @var int      $errorCount
         */
        [
            'snack' => $snack,
            'machine' => $machine,
            'entities' => $entities,
            'quantity' => $quantity,
            'errorCount' => $errorCount,
        ] = $createParams();
        /** @var TestValidator $validator */
        $validator = $this->service(TestValidator::class);
        $machineSnack = MachineSnackMother::fromEntities($machine, $snack);
        $this->loadEntities(
            $machine,
            $snack,
            $machineSnack,
            ...$entities,
        );
        $command = new UpdateMachineSnack(
            $machineSnack->id,
            $quantity,
        );

        // Act
        $errors = $validator->validate(
            $command,
            new NotEnoughQuantity(),
        );

        // Assert
        self::assertCount($errorCount, $errors->toArray());
    }

    /** @return iterable<string,array{\Closure}> */
    public static function quantityDataProvider(): iterable
    {
        yield 'higher than quantity in warehouse' => [
            static function (): array {
                $machine = MachineMother::random();
                $snack = SnackMother::random();
                $warehouseSnack = WarehouseSnackMother::fromSnack($snack);

                return [
                    'machine' => $machine,
                    'snack' => $snack,
                    'entities' => [
                        $warehouseSnack,
                    ],
                    'quantity' => $warehouseSnack->quantity + 1,
                    'errorCount' => 1,
                ];
            },
        ];

        yield 'lower than quantity in warehouse' => [
            static function (): array {
                $machine = MachineMother::random();
                $snack = SnackMother::random();
                $warehouseSnack = WarehouseSnackMother::fromSnack($snack);

                return [
                    'machine' => $machine,
                    'snack' => $snack,
                    'entities' => [
                        $warehouseSnack,
                    ],
                    'quantity' => $warehouseSnack->quantity - 1,
                    'errorCount' => 0,
                ];
            },
        ];

        yield 'same quantity in warehouse' => [
            static function (): array {
                $machine = MachineMother::random();
                $snack = SnackMother::random();
                $warehouseSnack = WarehouseSnackMother::fromSnack($snack);

                return [
                    'machine' => $machine,
                    'snack' => $snack,
                    'entities' => [
                        $warehouseSnack,
                    ],
                    'quantity' => $warehouseSnack->quantity,
                    'errorCount' => 0,
                ];
            },
        ];

        yield 'no data in warehouse' => [
            static function (): array {
                $machine = MachineMother::random();
                $snack = SnackMother::random();

                return [
                    'machine' => $machine,
                    'snack' => $snack,
                    'entities' => [],
                    'quantity' => Faker::int(),
                    'errorCount' => 1,
                ];
            },
        ];
    }
}
