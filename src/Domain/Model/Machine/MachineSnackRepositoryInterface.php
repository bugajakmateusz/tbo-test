<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Machine;

interface MachineSnackRepositoryInterface
{
    public function get(int $machineSnackId): MachineSnack;

    public function getByPosition(
        int $machineId,
        int $snackId,
        string $snackPosition,
    ): MachineSnack;
}
