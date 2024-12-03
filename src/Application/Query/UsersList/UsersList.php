<?php

declare(strict_types=1);

namespace Tab\Application\Query\UsersList;

use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\Filters;
use Tab\Packages\ResourcesList\Page;

final readonly class UsersList
{
    public function __construct(
        public Filters $filters,
        public Page $page,
        public ?Fields $fields = null,
    ) {
    }
}
