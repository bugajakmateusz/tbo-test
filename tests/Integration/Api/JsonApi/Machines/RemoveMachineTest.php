<?php

declare(strict_types=1);

namespace Polsl\Tests\Integration\Api\JsonApi\Machines;

use Polsl\Application\Schema\MachineSchema;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\JsonApi\Application\JsonApiKeywords;
use Polsl\Packages\TestCase\Mother\Entity\MachineMother;
use Polsl\Packages\TestCase\Mother\Entity\UserMother;
use Polsl\Tests\TestCase\JsonApiIntegrationTestCase;

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
