<?php

declare(strict_types=1);

namespace Polsl\Tests\Integration\Api\JsonApi\Users;

use Polsl\Application\Schema\UserSchema;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\JsonApi\Application\JsonApiKeywords;
use Polsl\Packages\TestCase\Fixtures\Entity\User;
use Polsl\Packages\TestCase\Mother\Entity\UserMother;
use Polsl\Tests\TestCase\Application\Client\JsonApi\JsonApiDocument;
use Polsl\Tests\TestCase\JsonApiIntegrationTestCase;

/** @internal */
final class UsersListTest extends JsonApiIntegrationTestCase
{
    public function test_not_logged_user_cannot_access_users_list(): void
    {
        // Arrange
        $user = UserMother::random();
        $this->loadEntities($user);
        $jsonApiClient = $this->jsonApiClient(
            UserSchema::class,
            $this->client(),
        );

        // Act
        $jsonApiResponse = $jsonApiClient->requestList();

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_UNAUTHORIZED, $jsonApiResponse->statusCode());
    }

    public function test_logged_admin_can_access_users_list(): void
    {
        // Arrange
        $user1 = UserMother::random();
        $loggedUser = UserMother::admin();
        $users = [$user1, $loggedUser];
        $this->loadEntities(
            ...$users,
        );
        $jsonApiClient = $this->loggedJsonApiClient(
            UserSchema::class,
            $loggedUser,
        );

        // Act
        $jsonApiResponse = $jsonApiClient->requestList();

        // Assert
        $jsonApiDocument = $jsonApiResponse->document();
        self::assertSame(HttpStatusCodes::HTTP_OK, $jsonApiResponse->statusCode());
        self::assertJsonApiType(UserSchema::TYPE, $jsonApiDocument);
        self::assertJsonApiItemsCount(2, $jsonApiDocument);
        self::assertJsonApiIds([$user1->id, $loggedUser->id], $jsonApiDocument);

        \usort(
            $users,
            static fn (User $left, User $right): int => $left->id <=> $right->id,
        );
        $firstResource = $jsonApiDocument->resourceAt(0);
        $this->assertUserAttributes($firstResource, $users[0]);
        $secondResource = $jsonApiDocument->resourceAt(1);
        $this->assertUserAttributes($secondResource, $users[1]);
    }

    public function test_filter_me(): void
    {
        // Arrange
        $user = UserMother::random();
        $users = [
            UserMother::random(),
            UserMother::random(),
            UserMother::random(),
            $user,
        ];
        $this->loadEntities(...$users);
        $jsonApiClient = $this->loggedJsonApiClient(
            UserSchema::class,
            $user,
        );

        // Act
        $jsonApiResponse = $jsonApiClient->requestList(
            additionalQueryParams: [
                JsonApiKeywords::FILTER => [
                    'me' => '1',
                ],
            ],
        );

        // Assert
        self::assertSame(HttpStatusCodes::HTTP_OK, $jsonApiResponse->statusCode());
        $document = $jsonApiResponse->document();
        $this->assertJsonApiIds([$user->id], $document);
        $resource = $document->resourceAt(0);
        $this->assertUserAttributes($resource, $user);
    }

    private function assertUserAttributes(
        JsonApiDocument $userResource,
        User $user,
    ): void {
        $this->assertJsonApiAttributes(
            $userResource,
            [
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'roles' => $user->roles,
            ],
        );
    }
}
