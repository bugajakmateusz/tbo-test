<?php

declare(strict_types=1);

namespace Polsl\Tests\Integration\Api\JsonApi\Buys;

use Polsl\Application\Schema\SnackBuySchema;
use Polsl\Application\Schema\SnackSchema;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\JsonApi\Application\JsonApiKeywords;
use Polsl\Packages\JsonApi\Application\Relationships;
use Polsl\Packages\TestCase\Mother\Entity\SnackMother;
use Polsl\Packages\TestCase\Mother\Entity\UserMother;
use Polsl\Packages\TestCase\Mother\Entity\WarehouseSnackMother;
use Polsl\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class BuySnackTest extends JsonApiIntegrationTestCase
{
    public function test_warehouse_operator_can_buy_snack(): void
    {
        // Arrange
        $loggedUser = UserMother::logisticManager();
        $snack = SnackMother::random();
        $warehouseSnack = WarehouseSnackMother::fromSnack($snack);
        $this->loadEntities(
            $loggedUser,
            $snack,
            $warehouseSnack,
        );
        $buySnackClient = $this->loggedJsonApiClient(
            SnackBuySchema::class,
            $loggedUser,
        );
        $relationships = Relationships::fromArray(
            \array_merge(
                $this->createRelationshipsData(
                    SnackBuySchema::RELATIONSHIP_SNACK,
                    (string) $snack->id,
                    SnackSchema::TYPE,
                ),
            ),
        );
        $price = Faker::float(min: 1.0);
        $quantity = Faker::int(1, 1000);

        // Act
        $response = $buySnackClient->createResource(
            [
                SnackBuySchema::ATTRIBUTE_PRICE => $price,
                SnackBuySchema::ATTRIBUTE_QUANTITY => $quantity,
            ],
            $relationships,
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_CREATED, $response->statusCode());
    }

    public function test_buy_price_validation(): void
    {
        // Arrange
        $loggedUser = UserMother::logisticManager();
        $snack = SnackMother::random();
        $this->loadEntities(
            $loggedUser,
            $snack,
        );
        $machineSnackClient = $this->loggedJsonApiClient(
            SnackBuySchema::class,
            $loggedUser,
        );
        $relationships = Relationships::fromArray(
            \array_merge(
                $this->createRelationshipsData(
                    SnackBuySchema::RELATIONSHIP_SNACK,
                    (string) $snack->id,
                    SnackSchema::TYPE,
                ),
            ),
        );
        $price = Faker::float(max: 0.0);
        $quantity = Faker::int(max: 0);

        // Act
        $response = $machineSnackClient->createResource(
            [
                SnackBuySchema::ATTRIBUTE_PRICE => $price,
                SnackBuySchema::ATTRIBUTE_QUANTITY => $quantity,
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
                    'Ta wartość powinna być większa niż 0.',
                ],
                'quantity' => [
                    'Ta wartość powinna być większa niż 0.',
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
