<?php

declare(strict_types=1);

namespace Tab\Packages\ResourcesList;

use Tab\Packages\ResourcesList\Filters\FiltersException;

final class Filters
{
    /** @var Filter[] */
    private readonly array $filters;

    private function __construct(Filter ...$filters)
    {
        $this->filters = $filters;
    }

    /** @param array<string,null|int|mixed[]|string> $filters */
    public static function fromArray(array $filters): self
    {
        $filterObjects = [];

        foreach ($filters as $name => $value) {
            $filterObjects[] = new Filter($name, $value);
        }

        return new self(...$filterObjects);
    }

    public static function fromFilters(Filter ...$filters): self
    {
        return new self(...$filters);
    }

    public function has(string $filterName): bool
    {
        $filters = $this->findFiltersByName($filterName);

        return \count($filters) >= 1;
    }

    /** @throws \Tab\Packages\ResourcesList\Filters\FiltersException */
    public function get(string $filterName): Filter
    {
        $filters = $this->findFiltersByName($filterName);

        if (0 === \count($filters)) {
            throw FiltersException::filterNotFound($filterName);
        }

        return \reset($filters);
    }

    /** @return Filter[] */
    public function toArray(): array
    {
        return $this->filters;
    }

    /** @return Filter[] */
    public function nonEmptyFilters(): array
    {
        return \array_filter(
            $this->filters,
            static function (Filter $filter): bool {
                $filterValue = $filter->value();

                if ('0' === $filterValue || 0 === $filterValue) {
                    return true;
                }

                return !empty($filterValue);
            },
        );
    }

    /** @throws \Tab\Packages\ResourcesList\Filters\FiltersException */
    public function checkSupportedFilters(string ...$supportedFilters): void
    {
        $filtersNames = \array_map(
            static fn (Filter $filter): string => $filter->name(),
            $this->filters,
        );

        $unsupportedFilters = \array_diff($filtersNames, $supportedFilters);

        if (\count($unsupportedFilters) > 0) {
            throw FiltersException::unsupportedFilters($supportedFilters, $unsupportedFilters);
        }
    }

    /** @return Filter[] */
    private function findFiltersByName(string $name): array
    {
        return \array_filter(
            $this->filters,
            static fn (Filter $filter): bool => $filter->name() === $name,
        );
    }
}
