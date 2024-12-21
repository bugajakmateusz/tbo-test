<?php

declare(strict_types=1);

namespace Tab\Application\Command\AddNewUser;

final readonly class AddNewUser
{
    /** @param array<string> $roles */
    public function __construct(
        public string $email,
        public string $password,
        public string $name,
        public string $surname,
        public array $roles = [],
    ) {}
}
