<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Login;

final readonly class LoggedUser
{
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
