<?php

declare(strict_types=1);

namespace Tab\Application\Command\AddNewMachineSnack;

use Tab\Domain\Model\Machine\MachineRepositoryInterface;
use Tab\Domain\Model\Machine\MachineSnack;
use Tab\Domain\Model\Machine\SnackPosition;
use Tab\Domain\Model\Snack\SnackRepositoryInterface;

final class AddNewMachineSnackHandler
{
    public function __construct(
        private MachineRepositoryInterface $machineRepository,
        private SnackRepositoryInterface $snackRepository,
    ) {
    }

    public function __invoke(AddNewMachineSnack $command): void
    {
        $machine = $this->machineRepository
            ->get($command->machineId)
        ;
        $snack = $this->snackRepository
            ->get($command->snackId)
        ;

        $machineSnack = MachineSnack::create(
            $machine,
            $snack,
            $command->quantity,
            SnackPosition::fromString($command->position),
        );

        $machine->addSnack($machineSnack);
    }
}
