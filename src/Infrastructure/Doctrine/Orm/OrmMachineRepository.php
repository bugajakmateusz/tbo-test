<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Polsl\Domain\EntityNotFoundException;
use Polsl\Domain\Model\Machine\Machine;
use Polsl\Domain\Model\Machine\MachineRepositoryInterface;

final readonly class OrmMachineRepository implements MachineRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

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
