<?php

declare(strict_types=1);

namespace Polsl\Application\Command\AddNewMachine;

use Polsl\Domain\Model\Machine\Location;
use Polsl\Domain\Model\Machine\Machine;
use Polsl\Domain\Model\Machine\MachineRepositoryInterface;

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
