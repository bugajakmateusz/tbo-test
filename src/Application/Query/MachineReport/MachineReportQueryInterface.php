<?php

declare(strict_types=1);

namespace Polsl\Application\Query\MachineReport;

use Polsl\Application\View\MachineReportView;

interface MachineReportQueryInterface
{
    /** @param string[] $machineIds */
    public function get(array $machineIds): MachineReportView;
}
