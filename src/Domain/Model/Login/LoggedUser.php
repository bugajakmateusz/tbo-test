<?php

declare(strict_types=1);

namespace Polsl\Domain\Model\Login;

use Polsl\Domain\DomainException;

final readonly class LoggedUser
{
    /** @param non-empty-string $username */
    private function __construct(
        private int $id,
        private string $username,
        private Roles $roles,
    ) {}

    /** @param array<string> $roles */
    public static function create(
        int $id,
        string $username,
        array $roles,
    ): self {
        $rolesCollection = Roles::fromStrings(...$roles);

        if (true === empty($username)) {
            throw new DomainException('Username cannot be empty.');
        }

        return new self(
            $id,
            $username,
            $rolesCollection,
        );
    }

    public function id(): int
    {
        return $this->id;
    }

    public function isIdEquals(int $id): bool
    {
        return $this->id === $id;
    }

    /** @return non-empty-string */
    public function username(): string
    {
        return $this->username;
    }

    /** @return string[] */
    public function roles(): array
    {
        return $this->roles
            ->toArray()
        ;
    }

    public function rolesCollection(): Roles
    {
        return $this->roles;
    }
}
