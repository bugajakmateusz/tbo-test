<?php

declare(strict_types=1);

namespace Tab\Tests\Integration\Api\JsonApi\Machines;

use Tab\Application\Schema\MachineSchema;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\Faker\Faker;
use Tab\Packages\JsonApi\Application\JsonApiKeywords;
use Tab\Packages\TestCase\Fixtures\Entity\Machine;
use Tab\Packages\TestCase\Mother\Entity\MachineMother;
use Tab\Packages\TestCase\Mother\Entity\UserMother;
use Tab\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class MachinesListTest extends JsonApiIntegrationTestCase
{
    public function test_not_logged_user_cannot_access_machines_list(): void
    {
        // Arrange
        $machine = MachineMother::random();
        $this->loadEntities($machine);
        $jsonApiClient = $this->jsonApiClient(
            MachineSchema::class,
            $this->client(),
        );

        // Act
        $jsonApiResponse = $jsonApiClient->requestList();

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_UNAUTHORIZED, $jsonApiResponse->statusCode());
    }

    public function test_logged_user_can_access_machines_list(): void
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
        $jsonApiResponse = $jsonApiClient->requestList();

        // Assert
        $jsonApiDocument = $jsonApiResponse->document();
        self::assertSame(HttpStatusCodes::HTTP_OK, $jsonApiResponse->statusCode());
        $this->assertJsonApiType(MachineSchema::TYPE, $jsonApiDocument);
        $this->assertJsonApiItemsCount(1, $jsonApiDocument);
        $this->assertJsonApiIds([$machine->id], $jsonApiDocument);
        $resource = $jsonApiDocument->resourceAt(0);
        $this->assertJsonApiAttributes(
            $resource,
            [
                'location' => $machine->location,
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
        $jsonApiResponse = $jsonApiClient->requestList(
            additionalQueryParams: [
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
        $jsonApiResponse = $jsonApiClient->requestList(
            additionalQueryParams: [JsonApiKeywords::FIELDS => $fields],
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_OK, $jsonApiResponse->statusCode());
        $document = $jsonApiResponse->document();
        $resource = $document->resourceAt(0);
        $this->assertJsonApiAttributes(
            $resource,
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
