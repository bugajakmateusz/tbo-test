<?php

declare(strict_types=1);

namespace Tab\Packages\ResourcesList\Filters;

use Tab\Packages\ResourcesList\Exception\ResourcesListException;

final class FiltersException extends ResourcesListException
{
    public static function filterNotFound(string $filterName): self
    {
        return new self("Filter with name '{$filterName}' not found.");
    }

    /**
     * @param string[] $supportedFilters
     * @param string[] $unsupportedFilters
     */
    public static function unsupportedFilters(array $supportedFilters, array $unsupportedFilters): self
    {
        $unsupportedFiltersString = \implode("', '", $unsupportedFilters);
        $supportedFiltersString = \implode("', '", $supportedFilters);

        return new self(
            "Unsupported filters found: '{$unsupportedFiltersString}', try one of these: '{$supportedFiltersString}'.",
        );
    }
}
