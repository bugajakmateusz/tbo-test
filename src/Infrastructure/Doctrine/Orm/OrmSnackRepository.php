<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Tab\Domain\EntityNotFoundException;
use Tab\Domain\Model\Snack\Snack;
use Tab\Domain\Model\Snack\SnackRepositoryInterface;

final readonly class OrmSnackRepository implements SnackRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function get(
        int $snackId,
    ): Snack {
        $snack = $this->entityManager
            ->find(
                Snack::class,
                $snackId,
            )
        ;

        if (null == $snack) {
            throw EntityNotFoundException::create(
                $snackId,
                Snack::class,
            );
        }

        return $snack;
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
