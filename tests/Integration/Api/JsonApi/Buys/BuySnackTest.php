<?php

declare(strict_types=1);

namespace Tab\Tests\Integration\Api\JsonApi\Buys;

use Tab\Application\Schema\SnackBuySchema;
use Tab\Application\Schema\SnackSchema;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\Faker\Faker;
use Tab\Packages\JsonApi\Application\JsonApiKeywords;
use Tab\Packages\JsonApi\Application\Relationships;
use Tab\Packages\TestCase\Mother\Entity\SnackMother;
use Tab\Packages\TestCase\Mother\Entity\UserMother;
use Tab\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class BuySnackTest extends JsonApiIntegrationTestCase
{
    public function test_warehouse_operator_can_buy_snack(): void
    {
        // Arrange
        $loggedUser = UserMother::warehouseOperator();
        $snack = SnackMother::random();
        $this->loadEntities(
            $loggedUser,
            $snack,
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

        // Act
        $response = $buySnackClient->createResource(
            [
                SnackBuySchema::ATTRIBUTE_PRICE => $price,
            ],
            $relationships,
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_CREATED, $response->statusCode());
    }

    public function test_buy_price_validation(): void
    {
        // Arrange
        $loggedUser = UserMother::warehouseOperator();
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
        $price = Faker::float(min: -100.0, max: 0.0);

        // Act
        $response = $machineSnackClient->createResource(
            [
                SnackBuySchema::ATTRIBUTE_PRICE => $price,
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
