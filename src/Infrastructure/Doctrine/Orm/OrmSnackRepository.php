<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Tab\Domain\Model\Snack\Snack;
use Tab\Domain\Model\Snack\SnackRepositoryInterface;

final readonly class OrmSnackRepository implements SnackRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function add(
        Snack $snack,
    ): void {
        $this->entityManager
            ->persist(
                $snack,
            )
        ;
    }
}
