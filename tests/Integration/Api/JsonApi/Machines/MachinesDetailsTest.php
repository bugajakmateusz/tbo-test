<?php

declare(strict_types=1);

namespace Integration\Api\JsonApi\Machines;

use Tab\Application\Schema\MachineSchema;
use Tab\Application\Schema\MachineSnackSchema;
use Tab\Application\Schema\SnackSchema;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\Faker\Faker;
use Tab\Packages\JsonApi\Application\JsonApiKeywords;
use Tab\Packages\TestCase\Fixtures\Entity\Machine;
use Tab\Packages\TestCase\Mother\Entity\MachineMother;
use Tab\Packages\TestCase\Mother\Entity\MachineSnackMother;
use Tab\Packages\TestCase\Mother\Entity\SnackMother;
use Tab\Packages\TestCase\Mother\Entity\UserMother;
use Tab\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class MachinesDetailsTest extends JsonApiIntegrationTestCase
{
    public function test_not_logged_user_cannot_access_machines_details(): void
    {
        // Arrange
        $machine = MachineMother::random();
        $this->loadEntities($machine);
        $jsonApiClient = $this->jsonApiClient(
            MachineSchema::class,
            $this->client(),
        );

        // Act
        $jsonApiResponse = $jsonApiClient->requestResource(
            (string) $machine->id,
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_UNAUTHORIZED, $jsonApiResponse->statusCode());
    }

    public function test_logged_user_can_access_machines_details(): void
    {
        // Arrange
        $machine = MachineMother::random();
        $loggedUser = UserMother::random();
        $this->loadEntities(
            $loggedUser,
            $machine,
        );
        $jsonApiClient = $this->loggedJsonApiClient(
            MachineSchema::class,
            $loggedUser,
        );

        // Act
        $jsonApiResponse = $jsonApiClient->requestResource(
            (string) $machine->id,
        );

        // Assert
        $jsonApiDocument = $jsonApiResponse->document();
        self::assertSame(HttpStatusCodes::HTTP_OK, $jsonApiResponse->statusCode());
        $this->assertJsonApiType(MachineSchema::TYPE, $jsonApiDocument);
        $this->assertJsonApiAttributes(
            $jsonApiDocument,
            [
                'location' => $machine->location,
                'positionsNumber' => $machine->positionNo,
                'positionsCapacity' => $machine->positionCapacity,
            ],
        );
    }

    public function test_machine_details_contains_information_about_snacks(): void
    {
        // Arrange
        $machine = MachineMother::random();
        $snack = SnackMother::random();
        $machineSnack = MachineSnackMother::fromEntities($machine, $snack);
        $loggedUser = UserMother::random();
        $this->loadEntities(
            $loggedUser,
            $machine,
            $snack,
            $machineSnack,
        );
        $jsonApiClient = $this->loggedJsonApiClient(
            MachineSchema::class,
            $loggedUser,
        );

        // Act
        $jsonApiResponse = $jsonApiClient->requestResource(
            (string) $machine->id,
            [
                JsonApiKeywords::FIELDS => [
                    'machines' => 'location,positionsNumber,positionsCapacity,machineSnacks',
                    'machine-snacks' => 'quantity,position,snack',
                    'snacks' => 'name',
                ],
                JsonApiKeywords::INCLUDE => 'machineSnacks,machineSnacks.snack',
            ],
        );

        // Assert
        $jsonApiDocument = $jsonApiResponse->document();
        self::assertSame(HttpStatusCodes::HTTP_OK, $jsonApiResponse->statusCode());
        self::assertJsonApiType(MachineSchema::TYPE, $jsonApiDocument);
        self::assertJsonApiAttributes(
            $jsonApiDocument,
            [
                'location' => $machine->location,
                'positionsNumber' => $machine->positionNo,
                'positionsCapacity' => $machine->positionCapacity,
            ],
        );
        self::assertJsonApiRelationships(
            $jsonApiDocument,
            [
                'machineSnacks' => [
                    [
                        'type' => MachineSnackSchema::TYPE,
                        'id' => (string) $machineSnack->id,
                    ],
                ],
            ],
        );
        $machineSnacksDocument = $jsonApiDocument->getInclude(
            (string) $machineSnack->id,
            MachineSnackSchema::TYPE,
        );
        self::assertJsonApiAttributes(
            $machineSnacksDocument,
            [
                'quantity' => $machineSnack->quantity,
                'position' => $machineSnack->position,
            ],
        );
        self::assertJsonApiRelationships(
            $machineSnacksDocument,
            [
                'snack' => [
                    'type' => SnackSchema::TYPE,
                    'id' => (string) $snack->id,
                ],
            ],
        );
        $snackDocument = $jsonApiDocument->getInclude(
            (string) $snack->id,
            SnackSchema::TYPE,
        );
        self::assertJsonApiAttributes(
            $snackDocument,
            [
                'name' => $snack->name,
            ],
        );
    }

    public function test_user_provided_fields_require_machines_type(): void
    {
        // Arrange
        $user = UserMother::random();
        $machine = MachineMother::random();
        $this->loadEntities($user, $machine);
        $jsonApiClient = $this->loggedJsonApiClient(
            MachineSchema::class,
            $user,
        );

        // Act
        $jsonApiResponse = $jsonApiClient->requestResource(
            (string) $machine->id,
            [
                JsonApiKeywords::FIELDS => [
                    Faker::word() => Faker::word(),
                ],
            ],
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR, $jsonApiResponse->statusCode());
        $response = $jsonApiResponse->response;
        /** @var array{detail?: string} $responseData */
        $responseData = $this->jsonDecode($response->content());
        self::assertSame(
            "When providing custom fields 'machines' type must be specified.",
            $responseData['detail'] ?? '',
        );
    }

    /**
     * @param \Closure(): array{
     *     machine: Machine,
     *     fields: array<string, string>,
     *     expectedAttributes: array<string, string|int>
     * } $paramsGenerator
     *
     * @dataProvider fieldsProvider
     */
    public function test_specifying_fields(\Closure $paramsGenerator): void
    {
        // Arrange
        [
            'machine' => $machine,
            'fields' => $fields,
            'expectedAttributes' => $expectedAttributes,
        ] = $paramsGenerator();
        $user = UserMother::random();
        $this->loadEntities($user, $machine);
        $jsonApiClient = $this->loggedJsonApiClient(
            MachineSchema::class,
            $user,
        );

        // Act
        $jsonApiResponse = $jsonApiClient->requestResource(
            (string) $machine->id,
            [JsonApiKeywords::FIELDS => $fields],
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_OK, $jsonApiResponse->statusCode());
        $this->assertJsonApiAttributes(
            $jsonApiResponse->document(),
            $expectedAttributes,
        );
    }

    /** @return iterable<string, array{\Closure}> */
    public static function fieldsProvider(): iterable
    {
        yield 'location attribute' => [
            static function (): array {
                $machine = MachineMother::random();

                return [
                    'machine' => $machine,
                    'fields' => [
                        'machines' => 'location',
                    ],
                    'expectedAttributes' => [
                        'location' => $machine->location,
                    ],
                ];
            },
        ];

        yield 'positions attributes' => [
            static function (): array {
                $machine = MachineMother::random();

                return [
                    'machine' => $machine,
                    'fields' => [
                        'machines' => 'positionsNumber, positionsCapacity',
                    ],
                    'expectedAttributes' => [
                        'positionsNumber' => $machine->positionNo,
                        'positionsCapacity' => $machine->positionCapacity,
                    ],
                ];
            },
        ];
    }
}
