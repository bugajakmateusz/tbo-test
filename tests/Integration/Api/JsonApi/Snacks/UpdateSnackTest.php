<?php

declare(strict_types=1);

namespace Polsl\Tests\Integration\Api\JsonApi\Snacks;

use Polsl\Application\Schema\SnackSchema;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\Mother\Entity\SnackMother;
use Polsl\Packages\TestCase\Mother\Entity\UserMother;
use Polsl\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class UpdateSnackTest extends JsonApiIntegrationTestCase
{
    public function test_logged_user_can_update_snack(): void
    {
        // Arrange
        $loggedUser = UserMother::officeManager();
        $snack = SnackMother::random();
        $this->loadEntities(
            $loggedUser,
            $snack,
        );
        $client = $this->loggedJsonApiClient(
            SnackSchema::class,
            $loggedUser,
        );
        $name = Faker::text();

        // Act
        $response = $client->updateResource(
            (string) $snack->id,
            [
                SnackSchema::ATTRIBUTE_NAME => $name,
            ],
        );

        // Assert
        self::assertSame(
            HttpStatusCodes::HTTP_NO_CONTENT,
            $response->statusCode(),
        );

        // Act
        $jsonApiResponse = $client->requestList();

        // Assert
        $document = $jsonApiResponse->document();
        $resource = $document->resourceAt(0);
        $this->assertJsonApiAttributes(
            $resource,
            [
                'name' => $name,
                'quantity' => 0,
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
    public function test_update_snack_validation(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'attributes' => $attributes,
            'errors' => $expectedErrors,
        ] = $createParams();
        $loggedUser = UserMother::officeManager();
        $snack = SnackMother::random();
        $this->loadEntities(
            $loggedUser,
            $snack,
        );
        $client = $this->loggedJsonApiClient(
            SnackSchema::class,
            $loggedUser,
        );

        // Act
        $response = $client->updateResource(
            (string) $snack->id,
            $attributes,
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
            $expectedErrors,
            $errors,
        );
    }

    /** @return iterable<string, array{\Closure}> */
    public static function validationDataProvider(): iterable
    {
        yield 'name empty' => [
            static fn (): array => [
                'attributes' => [
                    SnackSchema::ATTRIBUTE_NAME => '',
                ],
                'errors' => [
                    'name' => ['Ta wartość nie powinna być pusta.'],
                ],
            ],
        ];

        yield 'name too long' => [
            static fn (): array => [
                'attributes' => [
                    SnackSchema::ATTRIBUTE_NAME => Faker::hexBytes(256),
                ],
                'errors' => [
                    'name' => ['Ta wartość jest zbyt długa. Powinna mieć 255 lub mniej znaków.'],
                ],
            ],
        ];
    }
}
