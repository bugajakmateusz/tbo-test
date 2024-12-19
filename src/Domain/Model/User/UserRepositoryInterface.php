<?php

declare(strict_types=1);

namespace Tab\Domain\Model\User;

interface UserRepositoryInterface
{
    public function add(User $user): void;

    public function findByEmail(string $email): ?User;
}
