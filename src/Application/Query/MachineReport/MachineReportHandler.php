<?php

declare(strict_types=1);

namespace Tab\Application\Query\MachineReport;

use Tab\Application\View\MachineReportView;

final readonly class MachineReportHandler
{
    public function __construct(
        private MachineReportQueryInterface $machineReportQuery,
    ) {}

    public function __invoke(MachineReport $machineReport): MachineReportView
    {
        return $this->machineReportQuery
            ->get(
                $machineReport->machineIds,
            )
        ;
    }
}
