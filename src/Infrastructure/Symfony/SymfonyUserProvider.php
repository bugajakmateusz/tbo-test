<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Tab\Infrastructure\Symfony\Security\SymfonyUser;
use Tab\Packages\DbConnection\DbConnectionInterface;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;

final readonly class SymfonyUserProvider implements UserProviderInterface
{
    public function __construct(
        private DbConnectionInterface $dbConnection,
        private JsonSerializerInterface $jsonSerializer,
        private RoleHierarchyInterface $roleHierarchy,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        if (empty($identifier)) {
            throw new UserNotFoundException('Empty username provided.');
        }

        $sql = <<<'SQL'
            SELECT
                u.user_id AS id,
                u.email AS email,
                u.pass_hash AS pass_hash,
                u.roles AS roles
            FROM users AS u
            WHERE u.email = :username
            SQL;

        /**
         * @var false|array{
         *     id: int,
         *     email: string,
         *     pass_hash: string,
         *     roles: string,
         * } $userData
         */
        $userData = $this->dbConnection
            ->fetchAssociative(
                $sql,
                ['username' => $identifier],
            )
        ;

        if (false === $userData) {
            throw new UserNotFoundException("User with email '{$identifier}' not found.");
        }

        /** @var string[] $userRoles */
        $userRoles = $this->jsonSerializer
            ->decode($userData['roles'], true)
        ;

        return new SymfonyUser(
            (int) $userData['id'],
            $userData['email'],
            $userData['pass_hash'],
            $this->resolveRoles(...$userRoles),
        );
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        $class = $user::class;
        $isSupported = $this->supportsClass($class);
        if (!$isSupported) {
            throw new UnsupportedUserException("Class '{$class}' is not supported.");
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return SymfonyUser::class === $class;
    }

    /** @return string[] */
    private function resolveRoles(string ...$roles): array
    {
        $reachableRoles = $this->roleHierarchy
            ->getReachableRoleNames($roles)
        ;

        return \array_unique(
            \array_merge(
                $roles,
                $reachableRoles,
            ),
        );
    }
}
