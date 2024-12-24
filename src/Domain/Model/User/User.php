<?php

declare(strict_types=1);

namespace Tab\Domain\Model\User;

use Tab\Domain\DomainException;
use Tab\Domain\Email;

class User
{
    public int $id;

    /** @param Role[] $roles */
    private function __construct(
        public string $email,
        public string $pass_hash,
        public string $name,
        public string $surname,
        public array $roles,
    ) {}

    public static function register(
        Email $email,
        Password $password,
        Name $name,
        Name $surname,
        UserRepositoryInterface $userRepository,
        Role ...$roles,
    ): self {
        $stringEmail = $email->toString();
        $existingUser = $userRepository->findByEmail($stringEmail);
        if (null !== $existingUser) {
            throw new DomainException("User with email {$stringEmail} already exists");
        }

        if ([] === $roles) {
            throw new DomainException('Roles list should not be empty.');
        }
        $rolesWithUserRole = self::addUserRole($roles);

        return new self(
            $email->toString(),
            $password->toString(),
            $name->toString(),
            $surname->toString(),
            $rolesWithUserRole,
        );
    }

    public function changeEmail(
        Email $email,
        UserRepositoryInterface $userRepository,
    ): void {
        $stringEmail = $email->toString();
        $existingUser = $userRepository->findByEmail($stringEmail);
        if (null !== $existingUser) {
            throw new DomainException("User with email {$stringEmail} already exists");
        }
        $this->email = $stringEmail;
    }

    public function changePassword(Password $password): void
    {
        $this->pass_hash = $password->toString();
    }

    public function changeRoles(Role ...$roles): void
    {
        if ([] === $roles) {
            throw new DomainException('Roles list should not be empty.');
        }
        $rolesWithUserRole = self::addUserRole($roles);
        $this->roles = $rolesWithUserRole;
    }

    public function changeName(Name $name): void
    {
        $this->name = $name->toString();
    }

    public function changeSurname(Name $surname): void
    {
        $this->surname = $surname->toString();
    }

    /**
     * @param Role[] $roles
     *
     * @return Role[]
     */
    private static function addUserRole(array $roles): array
    {
        if (false === \in_array(Role::USER, $roles, true)) {
            $roles[] = Role::USER;
        }

        return $roles;
    }
}
