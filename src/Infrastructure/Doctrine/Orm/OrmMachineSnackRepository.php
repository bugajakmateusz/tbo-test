<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Tab\Domain\EntityNotFoundException;
use Tab\Domain\Model\Machine\Machine;
use Tab\Domain\Model\Machine\MachineSnack;
use Tab\Domain\Model\Machine\MachineSnackRepositoryInterface;

final readonly class OrmMachineSnackRepository implements MachineSnackRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function get(int $machineSnackId): MachineSnack
    {
        $machineSnack = $this->entityManager
            ->find(MachineSnack::class, $machineSnackId)
        ;

        if (null === $machineSnack) {
            throw EntityNotFoundException::create($machineSnackId, Machine::class);
        }

        return $machineSnack;
    }

    public function getByPosition(int $machineId, int $snackId, string $snackPosition): MachineSnack
    {
        $machineClass = MachineSnack::class;
        $query = $this->entityManager
            ->createQuery(
                <<<DQL
                    SELECT ms
                    FROM {$machineClass} AS ms
                    WHERE ms.machine = :machine
                        AND ms.snack = :snack
                        AND ms.position = :position
                        AND ms.quantity > 0
                    ORDER BY ms.id DESC

                    DQL
            )
        ;
        $query->setParameters(
            [
                'machine' => $machineId,
                'snack' => $snackId,
                'position' => $snackPosition,
            ],
        );
        $query->setMaxResults(1);

        /** @var MachineSnack $machineSnack */
        $machineSnack = $query->getSingleResult();

        return $machineSnack;
    }
}
