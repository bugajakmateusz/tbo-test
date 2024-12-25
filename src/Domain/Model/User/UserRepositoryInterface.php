<?php

declare(strict_types=1);

namespace Polsl\Domain\Model\User;

interface UserRepositoryInterface
{
    public function add(User $user): void;

    public function get(int $userId): User;

    public function findByEmail(string $email): ?User;
}
