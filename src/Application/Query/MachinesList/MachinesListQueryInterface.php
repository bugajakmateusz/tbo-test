<?php

declare(strict_types=1);

namespace Polsl\Application\Query\MachinesList;

use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\ResourcesList\Filters;
use Polsl\Packages\ResourcesList\Page;

interface MachinesListQueryInterface
{
    public function query(
        Page $page,
        Filters $filters,
        Fields $fields,
    ): MachinesListView;
}
