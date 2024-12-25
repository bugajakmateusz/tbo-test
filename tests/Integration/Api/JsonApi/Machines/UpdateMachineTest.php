<?php

declare(strict_types=1);

namespace Polsl\Tests\Integration\Api\JsonApi\Machines;

use Polsl\Application\Schema\MachineSchema;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\JsonApi\Application\JsonApiKeywords;
use Polsl\Packages\TestCase\Mother\Entity\MachineMother;
use Polsl\Packages\TestCase\Mother\Entity\UserMother;
use Polsl\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class UpdateMachineTest extends JsonApiIntegrationTestCase
{
    public function test_office_manager_can_update_machine(): void
    {
        // Arrange
        $loggedUser = UserMother::officeManager();
        $machine = MachineMother::random();
        $this->loadEntities(
            $loggedUser,
            $machine,
        );
        $client = $this->loggedJsonApiClient(
            MachineSchema::class,
            $loggedUser,
        );
        $location = Faker::word();
        $positionNo = Faker::intId();
        $positionCapacity = Faker::intId();

        // Act
        $response = $client->updateResource(
            (string) $machine->id,
            [
                MachineSchema::ATTRIBUTE_LOCATION => $location,
                MachineSchema::ATTRIBUTE_POSITIONS_NUMBER => $positionNo,
                MachineSchema::ATTRIBUTE_POSITIONS_CAPACITY => $positionCapacity,
            ],
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_NO_CONTENT, $response->statusCode());

        // Act
        $jsonApiResponse = $client->requestList(
            additionalQueryParams: [
                JsonApiKeywords::FIELDS => [
                    'machines' => 'positionsNumber, positionsCapacity, location',
                ],
            ],
        );

        // Assert
        $document = $jsonApiResponse->document();
        $resource = $document->resourceAt(0);
        $this->assertJsonApiAttributes(
            $resource,
            [
                'location' => $location,
                'positionsNumber' => $positionNo,
                'positionsCapacity' => $positionCapacity,
            ],
        );
    }

    public function test_some_machines_fields_can_be_updated(): void
    {
        // Arrange
        $loggedUser = UserMother::officeManager();
        $machine = MachineMother::random();
        $this->loadEntities(
            $loggedUser,
            $machine,
        );
        $client = $this->loggedJsonApiClient(
            MachineSchema::class,
            $loggedUser,
        );
        $location = Faker::word();

        // Act
        $response = $client->updateResource(
            (string) $machine->id,
            [
                MachineSchema::ATTRIBUTE_LOCATION => $location,
            ],
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_NO_CONTENT, $response->statusCode());

        // Act
        $jsonApiResponse = $client->requestList(
            additionalQueryParams: [
                JsonApiKeywords::FIELDS => [
                    'machines' => 'positionsNumber, positionsCapacity, location',
                ],
            ],
        );

        // Assert
        $document = $jsonApiResponse->document();
        $resource = $document->resourceAt(0);
        $this->assertJsonApiAttributes(
            $resource,
            [
                'location' => $location,
                'positionsNumber' => $machine->positionNo,
                'positionsCapacity' => $machine->positionCapacity,
            ],
        );
    }

    /**
     * @param \Closure(): array{
     *     attributes: array<string, string>,
     *     errors: array<string, string[]>,
     * } $createParams
     *
     * @dataProvider validationDataProvider
     */
    public function test_update_machine_validation(\Closure $createParams): void
    {
        // Arrange
        [
            'attributes' => $attributes,
            'errors' => $expectedErrors,
        ] = $createParams();
        $loggedUser = UserMother::officeManager();
        $machine = MachineMother::random();
        $this->loadEntities(
            $loggedUser,
            $machine,
        );
        $client = $this->loggedJsonApiClient(
            MachineSchema::class,
            $loggedUser,
        );

        // Act
        $response = $client->updateResource(
            (string) $machine->id,
            $attributes,
        );
        $responseData = $response->response;
        /**
         * @var array{
         *     errors?: array<string, string[]>,
         * } $responseDataContent
         */
        $responseDataContent = $this->jsonSerializer()
            ->decode($responseData->content(), true)
        ;
        $errors = $responseDataContent['errors'] ?? [];

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY, $response->statusCode());
        self::assertCount(\count($expectedErrors), $errors);
        self::assertEquals(
            $expectedErrors,
            $errors,
        );
    }

    /** @return iterable<string, array{\Closure}> */
    public static function validationDataProvider(): iterable
    {
        yield 'location empty' => [
            static fn (): array => [
                'attributes' => [
                    MachineSchema::ATTRIBUTE_LOCATION => '',
                ],
                'errors' => [
                    'location' => ['Ta wartość nie powinna być pusta.'],
                ],
            ],
        ];

        yield 'negative values' => [
            static fn (): array => [
                'attributes' => [
                    MachineSchema::ATTRIBUTE_POSITIONS_NUMBER => Faker::int(-100, -1),
                    MachineSchema::ATTRIBUTE_POSITIONS_CAPACITY => Faker::int(-100, -1),
                ],
                'errors' => [
                    'positionsNumber' => ['Ta wartość powinna być większa bądź równa 0.'],
                    'positionsCapacity' => ['Ta wartość powinna być większa bądź równa 0.'],
                ],
            ],
        ];
    }
}
