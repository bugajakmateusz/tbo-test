<?php

declare(strict_types=1);

namespace Integration\Api\JsonApi\MachineSnacks;

use Tab\Application\Schema\MachineSchema;
use Tab\Application\Schema\MachineSnackSchema;
use Tab\Application\Schema\SnackSchema;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\Faker\Faker;
use Tab\Packages\JsonApi\Application\JsonApiKeywords;
use Tab\Packages\JsonApi\Application\Relationships;
use Tab\Packages\TestCase\Mother\Entity\MachineMother;
use Tab\Packages\TestCase\Mother\Entity\MachineSnackMother;
use Tab\Packages\TestCase\Mother\Entity\SnackMother;
use Tab\Packages\TestCase\Mother\Entity\UserMother;
use Tab\Packages\TestCase\Mother\Entity\WarehouseSnackMother;
use Tab\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class AddNewMachineSnackTest extends JsonApiIntegrationTestCase
{
    public function test_logged_user_can_create_machine_snack(): void
    {
        // Arrange
        $loggedUser = UserMother::courier();
        $machine = MachineMother::random();
        $snack = SnackMother::random();
        $warehouseSnack = WarehouseSnackMother::fromSnack($snack);
        $this->loadEntities(
            $loggedUser,
            $machine,
            $snack,
            $warehouseSnack,
        );
        $machineSnackClient = $this->loggedJsonApiClient(
            MachineSnackSchema::class,
            $loggedUser,
        );
        $relationships = Relationships::fromArray(
            \array_merge(
                $this->createRelationshipsData(
                    MachineSnackSchema::RELATIONSHIP_SNACK,
                    (string) $snack->id,
                    SnackSchema::TYPE,
                ),
                $this->createRelationshipsData(
                    MachineSnackSchema::RELATIONSHIP_MACHINE,
                    (string) $machine->id,
                    MachineSchema::TYPE,
                ),
            ),
        );
        $position = Faker::hexBytes(3);
        $quantity = Faker::int(1, 50);

        // Act
        $response = $machineSnackClient->createResource(
            [
                MachineSnackSchema::ATTRIBUTE_POSITION => $position,
                MachineSnackSchema::ATTRIBUTE_QUANTITY => $quantity,
            ],
            $relationships,
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_CREATED, $response->statusCode());
    }

    /**
     * @param \Closure(): array{
     *     attributes: array<string, string>,
     *     errors: array<string, string[]>,
     *     entities: object[],
     *     snackId: int,
     *     machineId: int,
     * } $createParams
     *
     * @dataProvider validationDataProvider
     */
    public function test_add_machine_snack_validation(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'attributes' => $attributes,
            'errors' => $expectedErrors,
            'entities' => $entities,
            'snackId' => $snackId,
            'machineId' => $machineId,
        ] = $createParams();
        $loggedUser = UserMother::courier();
        $this->loadEntities(
            $loggedUser,
            ...$entities,
        );
        $client = $this->loggedJsonApiClient(
            MachineSnackSchema::class,
            $loggedUser,
        );
        $relationships = Relationships::fromArray(
            \array_merge(
                $this->createRelationshipsData(
                    MachineSnackSchema::RELATIONSHIP_SNACK,
                    (string) $snackId,
                    SnackSchema::TYPE,
                ),
                $this->createRelationshipsData(
                    MachineSnackSchema::RELATIONSHIP_MACHINE,
                    (string) $machineId,
                    MachineSchema::TYPE,
                ),
            ),
        );

        // Act
        $response = $client->createResource($attributes, $relationships);
        $responseData = $response->response;
        /**
         * @var array{
         *     errors?: array<string, string[]>,
         * } $responseDataContent
         */
        $responseDataContent = $this->jsonSerializer()
            ->decode(
                $responseData->content(),
                true,
            )
        ;
        $errors = $responseDataContent['errors'] ?? [];

        // Assert
        self::assertSame(
            HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            $response->statusCode(),
        );
        self::assertEquals(
            $expectedErrors,
            $errors,
        );
    }

    /** @return iterable<string, array{\Closure}> */
    public static function validationDataProvider(): iterable
    {
        yield 'misc #1' => [
            static fn (): array => [
                'attributes' => [
                    MachineSnackSchema::ATTRIBUTE_POSITION => '',
                    MachineSnackSchema::ATTRIBUTE_QUANTITY => Faker::int(max: 0),
                ],
                'errors' => [
                    MachineSnackSchema::ATTRIBUTE_POSITION => ['Ta wartość nie powinna być pusta.'],
                    MachineSnackSchema::ATTRIBUTE_QUANTITY => [
                        'Brak wystarczającej ilości produktu na magazynie.',
                        'Ta wartość powinna być większa niż 0.',
                    ],
                ],
                'entities' => [],
                'machineId' => Faker::intId(),
                'snackId' => Faker::intId(),
            ],
        ];

        yield 'position too long' => [
            static function (): array {
                $position = Faker::hexBytes(5);
                $machine = MachineMother::random();
                $snack = SnackMother::random();
                $warehouseSnack = WarehouseSnackMother::fromSnack($snack);

                return [
                    'attributes' => [
                        MachineSnackSchema::ATTRIBUTE_POSITION => $position,
                        MachineSnackSchema::ATTRIBUTE_QUANTITY => $warehouseSnack->quantity - 1,
                    ],
                    'errors' => [
                        MachineSnackSchema::ATTRIBUTE_POSITION => ['Ta wartość jest zbyt długa. Powinna mieć 3 lub mniej znaków.'],
                    ],
                    'entities' => [
                        $machine,
                        $snack,
                        $warehouseSnack,
                    ],
                    'machineId' => $machine->id,
                    'snackId' => $snack->id,
                ];
            },
        ];

        yield 'with taken position' => [
            static function (): array {
                $position = Faker::hexBytes(3);
                $machine = MachineMother::random();
                $snack = SnackMother::random();
                $warehouseSnack = WarehouseSnackMother::fromSnack($snack);

                return [
                    'attributes' => [
                        MachineSnackSchema::ATTRIBUTE_POSITION => $position,
                        MachineSnackSchema::ATTRIBUTE_QUANTITY => $warehouseSnack->quantity - 1,
                    ],
                    'errors' => [
                        MachineSnackSchema::ATTRIBUTE_POSITION => ['Na tej pozycji znajduje się już przekąska.'],
                    ],
                    'entities' => [
                        $machine,
                        $snack,
                        $warehouseSnack,
                        MachineSnackMother::fromEntities(
                            $machine,
                            $snack,
                            $position,
                        ),
                    ],
                    'machineId' => $machine->id,
                    'snackId' => $snack->id,
                ];
            },
        ];

        yield 'with too low product in warehourse' => [
            static function (): array {
                $machine = MachineMother::random();
                $snack = SnackMother::random();
                $warehouseSnack = WarehouseSnackMother::fromSnack($snack);

                return [
                    'attributes' => [
                        MachineSnackSchema::ATTRIBUTE_POSITION => Faker::hexBytes(3),
                        MachineSnackSchema::ATTRIBUTE_QUANTITY => $warehouseSnack->quantity + 1,
                    ],
                    'errors' => [
                        MachineSnackSchema::ATTRIBUTE_QUANTITY => ['Brak wystarczającej ilości produktu na magazynie.'],
                    ],
                    'entities' => [
                        $machine,
                        $snack,
                        $warehouseSnack,
                    ],
                    'machineId' => $machine->id,
                    'snackId' => $snack->id,
                ];
            },
        ];
    }

    /**
     * @return array<string, array{
     *     data: array{
     *         id: string,
     *         type: string,
     *     }
     * }>
     */
    private function createRelationshipsData(
        string $fieldName,
        string $id,
        string $type,
    ): array {
        return [
            $fieldName => [
                JsonApiKeywords::DATA => [
                    JsonApiKeywords::ID => $id,
                    JsonApiKeywords::TYPE => $type,
                ],
            ],
        ];
    }
}
