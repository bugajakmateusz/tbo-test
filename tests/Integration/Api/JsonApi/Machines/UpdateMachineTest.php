<?php

declare(strict_types=1);

namespace Tab\Tests\Integration\Api\JsonApi\Machines;

use Tab\Application\Schema\MachineSchema;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\Faker\Faker;
use Tab\Packages\JsonApi\Application\JsonApiKeywords;
use Tab\Packages\TestCase\Mother\Entity\MachineMother;
use Tab\Packages\TestCase\Mother\Entity\UserMother;
use Tab\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class UpdateMachineTest extends JsonApiIntegrationTestCase
{
    public function test_logged_user_can_update_machine(): void
    {
        // Arrange
        $loggedUser = UserMother::random();
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
        $loggedUser = UserMother::random();
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
                    'location' => ['This value should not be blank.'],
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
                    'positionsNumber' => ['This value should be greater than or equal to 0.'],
                    'positionsCapacity' => ['This value should be greater than or equal to 0.'],
                ],
            ],
        ];
    }
}
