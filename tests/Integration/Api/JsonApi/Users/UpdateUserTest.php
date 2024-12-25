<?php

declare(strict_types=1);

namespace Polsl\Tests\Integration\Api\JsonApi\Users;

use Polsl\Application\Schema\UserSchema;
use Polsl\Domain\Model\User\Role;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\Mother\Entity\UserMother;
use Polsl\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class UpdateUserTest extends JsonApiIntegrationTestCase
{
    public function test_not_logged_user_cannot_create_new_user(): void
    {
        // Arrange
        $jsonApiClient = $this->jsonApiClient(UserSchema::class);

        // Act
        $jsonApiResponse = $jsonApiClient->updateResource(
            Faker::stringNumberId(),
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_UNAUTHORIZED, $jsonApiResponse->statusCode());
    }

    public function test_not_admin_cannot_create_new_user(): void
    {
        // Arrange
        $loggedUser = UserMother::random();
        $this->loadEntities(
            $loggedUser,
        );
        $jsonApiClient = $this->loggedJsonApiClient(
            UserSchema::class,
            $loggedUser,
        );

        // Act
        $jsonApiResponse = $jsonApiClient->updateResource(
            (string) $loggedUser->id,
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_FORBIDDEN, $jsonApiResponse->statusCode());
    }

    public function test_logged_user_can_update_user(): void
    {
        // Arrange
        $loggedUser = UserMother::admin();
        $user = UserMother::random();
        $this->loadEntities(
            $loggedUser,
            $user,
        );
        $client = $this->loggedJsonApiClient(
            UserSchema::class,
            $loggedUser,
        );
        $email = Faker::email();
        $password = Faker::password();
        $roles = [Role::USER->value];

        // Act
        $response = $client->updateResource(
            (string) $user->id,
            [
                UserSchema::ATTRIBUTE_EMAIL => $email,
                UserSchema::ATTRIBUTE_PASSWORD => $password,
                UserSchema::ATTRIBUTE_ROLES => $roles,
            ],
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_NO_CONTENT, $response->statusCode());

        // Act
        $jsonApiResponse = $client->requestList();

        // Assert
        $document = $jsonApiResponse->document();
        $resource = $document->resourceAt(
            $loggedUser->id > $user->id ? 0 : 1,
        );
        $this->assertJsonApiAttributes(
            $resource,
            [
                'email' => $email,
                'name' => $user->name,
                'roles' => $roles,
                'surname' => $user->surname,
            ],
        );
    }

    /**
     * @param \Closure(): array{
     *     attributes: array<string, string>,
     *     errors: array<string, string[]>,
     *     entities: object[],
     * } $createParams
     *
     * @dataProvider validationDataProvider
     */
    public function test_update_user_validation(\Closure $createParams): void
    {
        // Arrange
        [
            'attributes' => $attributes,
            'errors' => $expectedErrors,
            'entities' => $entities,
        ] = $createParams();
        $loggedUser = UserMother::admin();
        $user = UserMother::random();
        $this->loadEntities(
            $loggedUser,
            $user,
            ...$entities,
        );
        $client = $this->loggedJsonApiClient(
            UserSchema::class,
            $loggedUser,
        );

        // Act
        $response = $client->updateResource(
            (string) $user->id,
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
        yield 'empty values' => [
            static fn (): array => [
                'attributes' => [
                    UserSchema::ATTRIBUTE_EMAIL => '',
                    UserSchema::ATTRIBUTE_PASSWORD => '',
                    UserSchema::ATTRIBUTE_ROLES => [],
                ],
                'errors' => [
                    'email' => [
                        'Ta wartość nie powinna być pusta.',
                    ],
                    'password' => [
                        'Ta wartość nie powinna być pusta.',
                        'Ta wartość jest zbyt krótka. Powinna mieć 8 lub więcej znaków.',
                    ],
                    'roles' => [
                        'Ta wartość nie powinna być pusta.',
                    ],
                ],
                'entities' => [],
            ],
        ];

        yield 'too long values' => [
            static fn (): array => [
                'attributes' => [
                    UserSchema::ATTRIBUTE_EMAIL => Faker::hexBytes(251),
                    UserSchema::ATTRIBUTE_PASSWORD => Faker::hexBytes(65),
                    UserSchema::ATTRIBUTE_ROLES => [Role::USER->value],
                ],
                'errors' => [
                    'email' => [
                        'Ta wartość jest zbyt długa. Powinna mieć 250 lub mniej znaków.',
                        'Ta wartość nie jest prawidłowym adresem email.',
                    ],
                    'password' => [
                        'Ta wartość jest zbyt długa. Powinna mieć 64 lub mniej znaków.',
                    ],
                ],
                'entities' => [],
            ],
        ];

        yield 'already existing email and invalid role' => [
            static function (): array {
                $email = Faker::email();

                return [
                    'attributes' => [
                        UserSchema::ATTRIBUTE_EMAIL => $email,
                        UserSchema::ATTRIBUTE_PASSWORD => Faker::password(),
                        UserSchema::ATTRIBUTE_ROLES => [Faker::word()],
                    ],
                    'errors' => [
                        'email' => [
                            'E-mail już istnieje.',
                        ],
                        'roles[0]' => [
                            'Ta wartość powinna być jedną z podanych opcji.',
                        ],
                    ],
                    'entities' => [
                        UserMother::withEmail($email),
                    ],
                ];
            },
        ];
    }
}
