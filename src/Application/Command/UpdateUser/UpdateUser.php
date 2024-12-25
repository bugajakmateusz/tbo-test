<?php

declare(strict_types=1);

namespace Polsl\Application\Command\UpdateUser;

final readonly class UpdateUser
{
    /** @param array<string> $roles */
    public function __construct(
        public int $id,
        public ?string $name,
        public ?string $surname,
        public ?string $email,
        public ?string $password,
        public ?array $roles,
    ) {}
}
