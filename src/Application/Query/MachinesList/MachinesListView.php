<?php

declare(strict_types=1);

namespace Polsl\Application\Query\MachinesList;

use Polsl\Application\View\MachineView;
use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\ResourcesList\TotalItems;

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
