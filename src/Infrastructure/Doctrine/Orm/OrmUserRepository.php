<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Tab\Domain\EntityNotFoundException;
use Tab\Domain\Model\User\User;
use Tab\Domain\Model\User\UserRepositoryInterface;

final class OrmUserRepository implements UserRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function add(User $user): void
    {
        $this->entityManager
            ->persist($user)
        ;
    }

    public function get(int $userId): User
    {
        $user = $this->entityManager
            ->find(
                User::class,
                $userId,
            )
        ;

        if (null === $user) {
            throw EntityNotFoundException::create(
                $userId,
                User::class,
            );
        }

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email])
        ;
    }
}
