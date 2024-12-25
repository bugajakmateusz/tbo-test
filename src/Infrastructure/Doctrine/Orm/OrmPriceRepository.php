<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Tab\Domain\Model\Machine\Machine;
use Tab\Domain\Model\Snack\Price;
use Tab\Domain\Model\Snack\PriceRepositoryInterface;
use Tab\Domain\Model\Snack\Snack;

final class OrmPriceRepository implements PriceRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function add(Price $price): void
    {
        $this->entityManager
            ->persist($price)
        ;
    }

    public function getActualPrice(Machine $machine, Snack $snack): Price
    {
        $priceClass = Price::class;
        $query = $this->entityManager
            ->createQuery(
                <<<DQL
                    SELECT p
                    FROM {$priceClass} AS p
                    WHERE p.machineId = :machineId
                        AND p.snackId = :snackId
                    ORDER BY p.createdAt DESC

                    DQL
            )
        ;
        $query->setParameters(
            [
                'machineId' => $machine->id(),
                'snackId' => $snack->id(),
            ],
        );
        $query->setMaxResults(1);

        /** @var Price $price */
        $price = $query->getSingleResult();

        return $price;
    }
}
