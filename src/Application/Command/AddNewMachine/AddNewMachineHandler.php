<?php

declare(strict_types=1);

namespace Tab\Application\Command\AddNewMachine;

use Tab\Domain\Model\Machine\Location;
use Tab\Domain\Model\Machine\Machine;
use Tab\Domain\Model\Machine\MachineRepositoryInterface;

final readonly class AddNewMachineHandler
{
    public function __construct(
        private MachineRepositoryInterface $machineRepository,
    ) {}

    public function __invoke(AddNewMachine $command): void
    {
        $machine = Machine::create(
            Location::fromString($command->location),
            $command->positionsNumber,
            $command->positionsCapacity,
        );

        $this->machineRepository
            ->add($machine)
        ;
    }
}
