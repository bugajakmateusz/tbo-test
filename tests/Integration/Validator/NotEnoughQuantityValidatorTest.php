<?php

declare(strict_types=1);

namespace Integration\Validator;

use Tab\Application\Command\AddNewMachineSnack\AddNewMachineSnack;
use Tab\Infrastructure\Symfony\Validator\Constraints\NotEnoughQuantity;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\IntegrationTestCase;
use Tab\Packages\TestCase\Mother\Entity\MachineMother;
use Tab\Packages\TestCase\Mother\Entity\SnackMother;
use Tab\Packages\TestCase\Mother\Entity\WarehouseSnackMother;
use Tab\Packages\TestCase\Validator\TestValidator;

/** @internal */
final class NotEnoughQuantityValidatorTest extends IntegrationTestCase
{
    /** @dataProvider quantityDataProvider */
    public function test_validate_quantity(\Closure $createParams): void
    {
        // Arrange
        /**
         * @var object[] $entities
         * @var int      $snackId
         * @var int      $quantity
         * @var int      $errorCount
         */
        [
            'entities' => $entities,
            'snackId' => $snackId,
            'quantity' => $quantity,
            'errorCount' => $errorCount,
        ] = $createParams();
        /** @var TestValidator $validator */
        $validator = $this->service(TestValidator::class);
        $this->loadEntities(...$entities);
        $command = new AddNewMachineSnack(
            Faker::intId(),
            $snackId,
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

    /** @return iterable<string,array{\Closure}> */
    public static function quantityDataProvider(): iterable
    {
        yield 'higher than quantity in warehouse' => [
            static function (): array {
                $machine = MachineMother::random();
                $snack = SnackMother::random();
                $warehouseSnack = WarehouseSnackMother::fromSnack($snack);

                return [
                    'entities' => [
                        $machine,
                        $snack,
                        $warehouseSnack,
                    ],
                    'snackId' => $snack->id,
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
                    'entities' => [
                        $machine,
                        $snack,
                        $warehouseSnack,
                    ],
                    'snackId' => $snack->id,
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
                    'entities' => [
                        $machine,
                        $snack,
                        $warehouseSnack,
                    ],
                    'snackId' => $snack->id,
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
                    'entities' => [
                        $machine,
                        $snack,
                    ],
                    'snackId' => $snack->id,
                    'quantity' => Faker::int(),
                    'errorCount' => 1,
                ];
            },
        ];
    }
}
