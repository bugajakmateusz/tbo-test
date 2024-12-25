<?php

declare(strict_types=1);

namespace Integration\Api\JsonApi\Prices;

use Polsl\Application\Schema\MachineSchema;
use Polsl\Application\Schema\SnackPriceSchema;
use Polsl\Application\Schema\SnackSchema;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\JsonApi\Application\JsonApiKeywords;
use Polsl\Packages\JsonApi\Application\Relationships;
use Polsl\Packages\TestCase\Mother\Entity\MachineMother;
use Polsl\Packages\TestCase\Mother\Entity\SnackMother;
use Polsl\Packages\TestCase\Mother\Entity\UserMother;
use Polsl\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class SetSnackPriceTest extends JsonApiIntegrationTestCase
{
    public function test_logged_user_can_set_snack_price(): void
    {
        // Arrange
        $loggedUser = UserMother::officeManager();
        $machine = MachineMother::random();
        $snack = SnackMother::random();
        $this->loadEntities(
            $loggedUser,
            $machine,
            $snack,
        );
        $machineSnackClient = $this->loggedJsonApiClient(
            SnackPriceSchema::class,
            $loggedUser,
        );
        $relationships = Relationships::fromArray(
            \array_merge(
                $this->createRelationshipsData(
                    SnackPriceSchema::RELATIONSHIP_SNACK,
                    (string) $snack->id,
                    SnackSchema::TYPE,
                ),
                $this->createRelationshipsData(
                    SnackPriceSchema::RELATIONSHIP_MACHINE,
                    (string) $machine->id,
                    MachineSchema::TYPE,
                ),
            ),
        );
        $price = Faker::float(min: 1.0);

        // Act
        $response = $machineSnackClient->createResource(
            [
                SnackPriceSchema::ATTRIBUTE_PRICE => $price,
            ],
            $relationships,
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_CREATED, $response->statusCode());
    }

    public function test_set_snack_price_validation(): void
    {
        // Arrange
        $loggedUser = UserMother::officeManager();
        $machine = MachineMother::random();
        $snack = SnackMother::random();
        $this->loadEntities(
            $loggedUser,
            $machine,
            $snack,
        );
        $machineSnackClient = $this->loggedJsonApiClient(
            SnackPriceSchema::class,
            $loggedUser,
        );
        $relationships = Relationships::fromArray(
            \array_merge(
                $this->createRelationshipsData(
                    SnackPriceSchema::RELATIONSHIP_SNACK,
                    (string) $snack->id,
                    SnackSchema::TYPE,
                ),
                $this->createRelationshipsData(
                    SnackPriceSchema::RELATIONSHIP_MACHINE,
                    (string) $machine->id,
                    MachineSchema::TYPE,
                ),
            ),
        );
        $price = Faker::float(min: -100.0, max: 0.0);

        // Act
        $response = $machineSnackClient->createResource(
            [
                SnackPriceSchema::ATTRIBUTE_PRICE => $price,
            ],
            $relationships,
        );
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
            [
                'price' => [
                    'Ta wartość powinna być dodatnia.',
                ],
            ],
            $errors,
        );
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
