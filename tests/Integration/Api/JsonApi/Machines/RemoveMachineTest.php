<?php

declare(strict_types=1);

namespace Tab\Tests\Integration\Api\JsonApi\Machines;

use Tab\Application\Schema\MachineSchema;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\JsonApi\Application\JsonApiKeywords;
use Tab\Packages\TestCase\Mother\Entity\MachineMother;
use Tab\Packages\TestCase\Mother\Entity\UserMother;
use Tab\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class RemoveMachineTest extends JsonApiIntegrationTestCase
{
    public function test_logged_user_can_remove_machine(): void
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

        // Act
        $response = $client->deleteResource(
            (string) $machine->id,
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_NO_CONTENT, $response->statusCode());

        // Act
        $jsonApiResponse = $client->requestList(
            additionalQueryParams: [
                JsonApiKeywords::FIELDS => [
                ],
            ],
        );

        // Assert
        $document = $jsonApiResponse->document();
        self::assertSame(0, $document->resourcesCount());
    }
}
