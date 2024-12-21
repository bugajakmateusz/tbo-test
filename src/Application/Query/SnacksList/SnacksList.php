<?php

declare(strict_types=1);

namespace Tab\Application\Query\SnacksList;

use Tab\Packages\ResourcesList\Filters;
use Tab\Packages\ResourcesList\Page;

final readonly class SnacksList
{
    public function __construct(
        public Filters $filters,
        public Page $page,
    ) {}
}
