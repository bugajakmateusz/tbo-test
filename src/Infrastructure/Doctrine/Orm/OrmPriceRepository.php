<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Tab\Domain\Model\Snack\Price;
use Tab\Domain\Model\Snack\PriceRepositoryInterface;

final class OrmPriceRepository implements PriceRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function add(Price $price): void
    {
        $this->entityManager
            ->persist($price)
        ;
    }
}
