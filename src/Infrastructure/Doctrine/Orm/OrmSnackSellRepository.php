<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Tab\Domain\Model\Snack\SnackSell;
use Tab\Domain\Model\Snack\SnackSellRepositoryInterface;

final readonly class OrmSnackSellRepository implements SnackSellRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function add(SnackSell $snackSell): void
    {
        $this->entityManager
            ->persist($snackSell)
        ;
    }
}
