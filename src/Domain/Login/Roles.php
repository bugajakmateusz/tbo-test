<?php

declare(strict_types=1);

namespace Tab\Domain\Login;

final readonly class Roles
{
    /** @var string[] */
    private array $roles;

    private function __construct(string ...$roles)
    {
        $this->roles = $roles;
    }

    public static function fromStrings(string &...$roles): self
    {
        $roles[] = 'ROLE_USER';

        return new self(...\array_unique($roles));
    }

    public function hasRole(string $role): bool
    {
        return \in_array(
            $role,
            $this->roles,
            true,
        );
    }

    /** @return string[] */
    public function toArray(): array
    {
        return $this->roles;
    }
}
