<?php

declare(strict_types=1);

namespace Tab\Tests\Integration\Api\JsonApi\Machines;

use Tab\Application\Schema\MachineSchema;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\Faker\Faker;
use Tab\Packages\JsonApi\Application\JsonApiKeywords;
use Tab\Packages\TestCase\Mother\Entity\UserMother;
use Tab\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class AddNewMachineTest extends JsonApiIntegrationTestCase
{
    public function test_office_manager_can_create_machine(): void
    {
        // Arrange
        $loggedUser = UserMother::officeManager();
        $this->loadEntities(
            $loggedUser,
        );
        $client = $this->loggedJsonApiClient(
            MachineSchema::class,
            $loggedUser,
        );
        $location = Faker::word();
        $positionNo = Faker::intId();
        $positionCapacity = Faker::intId();

        // Act
        $response = $client->createResource(
            [
                MachineSchema::ATTRIBUTE_LOCATION => $location,
                MachineSchema::ATTRIBUTE_POSITIONS_NUMBER => $positionNo,
                MachineSchema::ATTRIBUTE_POSITIONS_CAPACITY => $positionCapacity,
            ],
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_CREATED, $response->statusCode());

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

    public function test_add_machine_validation(): void
    {
        // Arrange
        $loggedUser = UserMother::officeManager();
        $this->loadEntities(
            $loggedUser,
        );
        $client = $this->loggedJsonApiClient(
            MachineSchema::class,
            $loggedUser,
        );
        $location = '';
        $positionNo = Faker::int(-100, -1);
        $positionCapacity = Faker::int(-100, -1);

        // Act
        $response = $client->createResource(
            [
                MachineSchema::ATTRIBUTE_LOCATION => $location,
                MachineSchema::ATTRIBUTE_POSITIONS_NUMBER => $positionNo,
                MachineSchema::ATTRIBUTE_POSITIONS_CAPACITY => $positionCapacity,
            ],
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
        self::assertCount(3, $errors);
        self::assertEquals(
            [
                'location' => ['Ta wartość nie powinna być pusta.'],
                'positionsNumber' => ['Ta wartość powinna być większa bądź równa 0.'],
                'positionsCapacity' => ['Ta wartość powinna być większa bądź równa 0.'],
            ],
            $errors,
        );
    }
}
