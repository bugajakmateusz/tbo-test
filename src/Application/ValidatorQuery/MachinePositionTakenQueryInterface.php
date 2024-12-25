<?php

declare(strict_types=1);

namespace Polsl\Application\ValidatorQuery;

interface MachinePositionTakenQueryInterface
{
    public function query(string $position, int $machineId): bool;
}
