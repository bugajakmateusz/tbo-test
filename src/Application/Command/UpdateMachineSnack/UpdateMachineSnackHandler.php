<?php

declare(strict_types=1);

namespace Tab\Application\Command\UpdateMachineSnack;

use Tab\Domain\Model\Machine\MachineSnackRepositoryInterface;
use Tab\Domain\Service\ClockInterface;

final readonly class UpdateMachineSnackHandler
{
    public function __construct(
        private MachineSnackRepositoryInterface $machineSnackRepository,
        private ClockInterface $clock,
    ) {}

    public function __invoke(UpdateMachineSnack $command): void
    {
        $machineSnack = $this->machineSnackRepository
            ->get($command->id)
        ;

        $machineSnack->updateQuantity(
            $command->quantity,
            $this->clock,
        );
    }
}
