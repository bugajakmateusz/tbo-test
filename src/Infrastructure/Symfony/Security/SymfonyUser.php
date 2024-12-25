<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Symfony\Security;

use Polsl\Domain\Model\Login\LoggedUser;
use Polsl\Infrastructure\Symfony\Security\UserInterface as AppUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class SymfonyUser implements AppUserInterface, EquatableInterface, PasswordAuthenticatedUserInterface
{
    private readonly LoggedUser $user;

    /** @param string[] $roles */
    public function __construct(
        int $id,
        string $username,
        private string $passwordHash,
        array $roles,
    ) {
        $this->user = LoggedUser::create(
            $id,
            $username,
            $roles,
        );
    }

    /** @return string[] */
    public function getRoles(): array
    {
        return $this->user
            ->roles()
        ;
    }

    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    public function getUserIdentifier(): string
    {
        return $this->user
            ->username()
        ;
    }

    public function eraseCredentials(): void
    {
        // no-op
    }

    public function loggedUser(): LoggedUser
    {
        return $this->user;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        $isSameClass = self::class === $user::class;
        $isSameUserName = $user->getUserIdentifier() === $this->getUserIdentifier();
        $isSamePassword = \method_exists($user, 'getPassword')
            && $user->getPassword() === $this->getPassword()
        ;
        $roles = $this->getRoles();
        $userRoles = $user->getRoles();
        $isSameRoles = \count($roles) === \count($userRoles)
            && \count(\array_intersect($roles, $userRoles)) === \count($roles)
        ;

        return $isSameClass
            && $isSameUserName
            && $isSamePassword
            && $isSameRoles
        ;
    }
}
