<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Fixtures\Entity;

final class User
{
    /** @param string[] $roles */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $surname,
        public readonly string $email,
        public readonly string $password,
        public readonly string $passwordHash,
        public readonly array $roles = [],
    ) {
    }
}
