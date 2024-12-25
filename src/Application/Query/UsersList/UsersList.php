<?php

declare(strict_types=1);

namespace Polsl\Application\Query\UsersList;

use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\ResourcesList\Filters;
use Polsl\Packages\ResourcesList\Page;

final readonly class UsersList
{
    public function __construct(
        public Filters $filters,
        public Page $page,
        public ?Fields $fields = null,
    ) {}
}
