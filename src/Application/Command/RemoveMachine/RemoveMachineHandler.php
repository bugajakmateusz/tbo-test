<?php

declare(strict_types=1);

namespace Tab\Application\Command\RemoveMachine;

use Tab\Domain\Model\Machine\MachineRepositoryInterface;

final readonly class RemoveMachineHandler
{
    public function __construct(
        private MachineRepositoryInterface $machineRepository,
    ) {}

    public function __invoke(RemoveMachine $command): void
    {
        $machine = $this->machineRepository
            ->get($command->id)
        ;
        $this->machineRepository
            ->remove($machine)
        ;
    }
}
