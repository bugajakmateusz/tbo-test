<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Mock;

use Tab\Domain\EntityNotFoundException;
use Tab\Domain\Model\User\User;
use Tab\Domain\Model\User\UserRepositoryInterface;

final readonly class FakeUserRepository implements UserRepositoryInterface
{
    public function __construct(private ?User $user = null) {}

    public function add(User $user): void {}

    public function get(int $userId): User
    {
        $user = $this->user;
        if (null == $user) {
            throw EntityNotFoundException::create(
                $userId,
                User::class,
            );
        }

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->user;
    }
}
