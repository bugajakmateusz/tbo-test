<?php

declare(strict_types=1);

namespace Polsl\Application\Command\AddNewMachineSnack;

use Polsl\Domain\Model\Machine\MachineRepositoryInterface;
use Polsl\Domain\Model\Machine\MachineSnack;
use Polsl\Domain\Model\Machine\SnackPosition;
use Polsl\Domain\Model\Snack\SnackRepositoryInterface;
use Polsl\Domain\Service\ClockInterface;

final readonly class AddNewMachineSnackHandler
{
    public function __construct(
        private MachineRepositoryInterface $machineRepository,
        private SnackRepositoryInterface $snackRepository,
        private ClockInterface $clock,
    ) {}

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
            $this->clock,
        );

        $machine->addSnack($machineSnack);
    }
}
