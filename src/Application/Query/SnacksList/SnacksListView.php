<?php

declare(strict_types=1);

namespace Tab\Application\Query\SnacksList;

use Tab\Application\View\SnackView;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\TotalItems;

final readonly class SnacksListView
{
    /** @var SnackView[] */
    public array $snacks;

    public function __construct(
        public TotalItems $totalItems,
        public Fields $fields,
        SnackView ...$snackView,
    ) {
        $this->snacks = $snackView;
    }
}
