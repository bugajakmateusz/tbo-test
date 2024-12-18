<?php

declare(strict_types=1);

namespace Tab\Application\Query\SnacksList;

use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\Filters;
use Tab\Packages\ResourcesList\Page;

interface SnacksListQueryInterface
{
    public function query(
        Page $page,
        Filters $filters,
        Fields $fields,
    ): SnacksListView;
}
