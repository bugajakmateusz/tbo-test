<?php

declare(strict_types=1);

namespace Polsl\Application\Query\SnacksList;

use Polsl\Application\View\SnackView;
use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\ResourcesList\TotalItems;

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
