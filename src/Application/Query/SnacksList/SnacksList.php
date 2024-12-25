<?php

declare(strict_types=1);

namespace Polsl\Application\Query\SnacksList;

use Polsl\Packages\ResourcesList\Filters;
use Polsl\Packages\ResourcesList\Page;

final readonly class SnacksList
{
    public function __construct(
        public Filters $filters,
        public Page $page,
    ) {}
}
