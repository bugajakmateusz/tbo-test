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
}
