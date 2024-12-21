<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Fixtures\Entity;

final readonly class User
{
    /** @param string[] $roles */
    public function __construct(
        public int $id,
        public string $name,
        public string $surname,
        public string $email,
        public string $password,
        public string $passwordHash,
        public array $roles = [],
    ) {}
}
