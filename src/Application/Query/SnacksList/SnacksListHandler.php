<?php

declare(strict_types=1);

namespace Tab\Application\Query\SnacksList;

use Tab\Application\Schema\SnackSchema;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\Filters;

final readonly class SnacksListHandler
{
    public const FILTER_NAME = 'name';
    private const SUPPORTED_FILTERS = [
        self::FILTER_NAME,
    ];

    public function __construct(
        private SnacksListQueryInterface $snacksListQuery,
    ) {
    }

    public function __invoke(
        SnacksList $snacksList,
    ): SnacksListView {
        $filters = $snacksList->filters;
        $filters->checkSupportedFilters(...self::SUPPORTED_FILTERS);
        $nonEmptyFilters = Filters::fromFilters(...$filters->nonEmptyFilters());

        return $this->snacksListQuery
            ->query(
                $snacksList->page,
                $nonEmptyFilters,
                Fields::createAllFieldsForType(SnackSchema::TYPE),
            )
        ;
    }
}
