<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Polsl\Domain\Model\Snack\Buy;
use Polsl\Domain\Model\Snack\BuyRepositoryInterface;

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
