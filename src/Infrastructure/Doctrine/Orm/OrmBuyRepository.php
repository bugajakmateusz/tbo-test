<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Tab\Domain\Model\Snack\Buy;
use Tab\Domain\Model\Snack\BuyRepositoryInterface;

final readonly class OrmBuyRepository implements BuyRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function add(Buy $buy): void
    {
        $this->entityManager
            ->persist($buy)
        ;
    }
}
