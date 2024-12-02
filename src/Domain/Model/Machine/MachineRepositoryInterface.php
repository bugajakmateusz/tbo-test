<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Machine;

interface MachineRepositoryInterface
{
    public function get(int $machineId): Machine;

    public function add(Machine $machine): void;

    public function remove(Machine $machine): void;
}
