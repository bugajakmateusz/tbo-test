<?php

declare(strict_types=1);

namespace Tab\Packages\DbConnection;

use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineOrmEntityManager implements OrmEntityManagerInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function flush(): void
    {
        $this->entityManager
            ->flush()
        ;
    }

    public function wrapInTransaction(\Closure $callable): mixed
    {
        return $this->entityManager
            ->wrapInTransaction($callable)
        ;
    }

    public function beginTransaction(): void
    {
        $this->entityManager
            ->beginTransaction()
        ;
    }

    public function commit(): void
    {
        $this->entityManager
            ->commit()
        ;
    }

    public function rollback(): void
    {
        $this->entityManager
            ->rollback()
        ;
    }

    public function clear(): void
    {
        $this->entityManager
            ->clear()
        ;
    }
}
