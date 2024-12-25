<?php

declare(strict_types=1);

namespace Polsl\Application\Query\MachineReport;

final readonly class MachineReport
{
    /** @param string[] $machineIds */
    public function __construct(
        public array $machineIds,
    ) {}
}
