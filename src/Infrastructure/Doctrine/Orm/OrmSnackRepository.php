<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Polsl\Domain\EntityNotFoundException;
use Polsl\Domain\Model\Snack\Snack;
use Polsl\Domain\Model\Snack\SnackRepositoryInterface;

final readonly class OrmSnackRepository implements SnackRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

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
