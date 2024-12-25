<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Polsl\Domain\Model\Snack\SnackSell;
use Polsl\Domain\Model\Snack\SnackSellRepositoryInterface;

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
