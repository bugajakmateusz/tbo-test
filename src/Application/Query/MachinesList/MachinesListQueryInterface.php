<?php

declare(strict_types=1);

namespace Tab\Application\Query\MachinesList;

use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\Filters;
use Tab\Packages\ResourcesList\Page;

interface MachinesListQueryInterface
{
    public function query(
        Page $page,
        Filters $filters,
        Fields $fields,
    ): MachinesListView;
}
