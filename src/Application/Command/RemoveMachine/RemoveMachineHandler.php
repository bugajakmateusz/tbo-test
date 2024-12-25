<?php

declare(strict_types=1);

namespace Polsl\Application\Command\RemoveMachine;

use Polsl\Domain\Model\Machine\MachineRepositoryInterface;

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
