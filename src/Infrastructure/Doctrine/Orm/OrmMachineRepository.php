<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Tab\Domain\EntityNotFoundException;
use Tab\Domain\Model\Machine\Machine;
use Tab\Domain\Model\Machine\MachineRepositoryInterface;

final readonly class OrmMachineRepository implements MachineRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function get(int $machineId): Machine
    {
        $machine = $this->entityManager
            ->find(Machine::class, $machineId)
        ;

        if (null === $machine) {
            throw EntityNotFoundException::create($machineId, Machine::class);
        }

        return $machine;
    }

    public function add(Machine $machine): void
    {
        $this->entityManager
            ->persist($machine)
        ;
    }

    public function remove(Machine $machine): void
    {
        $this->entityManager
            ->remove($machine)
        ;
    }
}
