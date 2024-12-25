<?php

declare(strict_types=1);

namespace Tab\Application\Query\MachineReport;

use Tab\Application\View\MachineReportView;

interface MachineReportQueryInterface
{
    /** @param string[] $machineIds */
    public function get(array $machineIds): MachineReportView;
}
