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
final class CreateNewUserTest extends JsonApiIntegrationTestCase
{
    public function test_not_logged_user_cannot_create_new_user(): void
    {
        // Arrange
        $jsonApiClient = $this->jsonApiClient(UserSchema::class);

        // Act
        $jsonApiResponse = $jsonApiClient->createResource();

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
        $jsonApiResponse = $jsonApiClient->createResource();

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_FORBIDDEN, $jsonApiResponse->statusCode());
    }

    public function test_admin_can_create_user_with_valid_data(): void
    {
        // Arrange
        $loggedUser = UserMother::admin();
        $this->loadEntities(
            $loggedUser,
        );
        $jsonApiClient = $this->loggedJsonApiClient(
            UserSchema::class,
            $loggedUser,
        );

        // Act
        $jsonApiResponse = $jsonApiClient->createResource(
            [
                UserSchema::ATTRIBUTE_EMAIL => Faker::email(),
                UserSchema::ATTRIBUTE_PASSWORD => Faker::password(),
                UserSchema::ATTRIBUTE_NAME => Faker::firstName(),
                UserSchema::ATTRIBUTE_SURNAME => Faker::lastName(),
                UserSchema::ATTRIBUTE_ROLES => ['ROLE_USER'],
            ],
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_CREATED, $jsonApiResponse->statusCode());
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
    public function test_create_user_validation(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'attributes' => $attributes,
            'errors' => $expectedErrors,
            'entities' => $entities,
        ] = $createParams();
        $loggedUser = UserMother::admin();
        $this->loadEntities(
            $loggedUser,
            ...$entities,
        );
        $jsonApiClient = $this->loggedJsonApiClient(
            UserSchema::class,
            $loggedUser,
        );

        // Act
        $jsonApiResponse = $jsonApiClient->createResource($attributes);
        $responseData = $jsonApiResponse->response;
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
        self::assertSame(HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY, $jsonApiResponse->statusCode());
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
                    UserSchema::ATTRIBUTE_NAME => '',
                    UserSchema::ATTRIBUTE_SURNAME => '',
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
                    'name' => [
                        'Ta wartość nie powinna być pusta.',
                    ],
                    'surname' => [
                        'Ta wartość nie powinna być pusta.',
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
                    UserSchema::ATTRIBUTE_NAME => Faker::hexBytes(21),
                    UserSchema::ATTRIBUTE_SURNAME => Faker::hexBytes(21),
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
                    'name' => [
                        'Ta wartość jest zbyt długa. Powinna mieć 20 lub mniej znaków.',
                    ],
                    'surname' => [
                        'Ta wartość jest zbyt długa. Powinna mieć 20 lub mniej znaków.',
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
                        UserSchema::ATTRIBUTE_NAME => Faker::firstName(),
                        UserSchema::ATTRIBUTE_SURNAME => Faker::lastName(),
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
