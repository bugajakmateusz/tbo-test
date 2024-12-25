<?php

declare(strict_types=1);

namespace Polsl\Application\Command\UpdateMachineSnack;

use Polsl\Domain\Model\Machine\MachineSnackRepositoryInterface;
use Polsl\Domain\Service\ClockInterface;

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
