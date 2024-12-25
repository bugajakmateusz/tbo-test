<?php

declare(strict_types=1);

namespace Polsl\Tests\Unit\Infrastructure\Symfony;

use Polsl\Infrastructure\Symfony\Security\SymfonyUser;
use Polsl\Infrastructure\Symfony\SymfonyUserProvider;
use Polsl\Packages\DbConnection\DbConnectionInterface;
use Polsl\Packages\JsonSerializer\JsonSerializerInterface;
use Polsl\Packages\TestCase\UnitTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/** @internal */
final class SymfonyUserProviderTest extends UnitTestCase
{
    public function test_provider_supports_user_class(): void
    {
        // Arrange
        $userProvider = $this->createProvider();

        // Assert
        self::assertTrue($userProvider->supportsClass(SymfonyUser::class));
    }

    public function test_provider_fails_when_refreshing_unsupported_user(): void
    {
        // Arrange
        $userProvider = $this->createProvider();
        $user = $this->createMock(UserInterface::class);

        // Expect
        $this->expectException(UnsupportedUserException::class);

        // Act
        $userProvider->refreshUser($user);
    }

    private function createProvider(
        ?DbConnectionInterface $dbConnection = null,
    ): SymfonyUserProvider {
        return new SymfonyUserProvider(
            $dbConnection ?? $this->createMock(DbConnectionInterface::class),
            $this->createMock(JsonSerializerInterface::class),
            $this->createMock(RoleHierarchyInterface::class),
        );
    }
}
