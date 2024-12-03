<?php

declare(strict_types=1);

namespace Tab\Application\Query\MachinesList;

use Tab\Application\View\MachineView;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\TotalItems;

final readonly class MachinesListView
{
    /** @var MachineView[] */
    public array $machines;

    public function __construct(
        public TotalItems $totalItems,
        public Fields $fields,
        MachineView ...$machineView,
    ) {
        $this->machines = $machineView;
    }
}
