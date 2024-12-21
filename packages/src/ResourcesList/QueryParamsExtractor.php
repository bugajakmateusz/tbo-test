<?php

declare(strict_types=1);

namespace Tab\Packages\ResourcesList;

final class QueryParamsExtractor
{
    public const PARAM_FILTER = 'filter';
    public const PARAM_PAGE = 'page';
    public const PARAM_INCLUDE = 'include';
    public const PARAM_SORT = 'sort';
    public const PARAM_FIELDS = 'fields';

    /**
     * @param array{
     *     filter?: null|array<
     *         string,
     *         null|int|mixed[]|string
     *     >,
     *     page?: null|array{
     *         number?: string|int,
     *         size?: string|int,
     *     },
     *     include?: null|string,
     *     sort?: null|string,
     *     fields?: null|array<
     *         string,
     *         string
     *     >,
     * } $queryParams
     */
    public function __construct(private readonly array $queryParams) {}

    public function filters(): Filters
    {
        $filtersData = $this->queryParams[self::PARAM_FILTER] ?? [];

        return Filters::fromArray($filtersData);
    }

    public function page(): Page
    {
        $pageData = $this->queryParams[self::PARAM_PAGE] ?? [];

        return Page::fromArray($pageData);
    }

    public function includes(): Includes
    {
        $includesData = $this->queryParams[self::PARAM_INCLUDE] ?? '';

        return Includes::fromJsonApiString($includesData);
    }

    public function sorts(): Sorts
    {
        $sortData = $this->queryParams[self::PARAM_SORT] ?? '';

        return Sorts::fromJsonApiString($sortData);
    }

    public function fields(): Fields
    {
        $fieldsData = $this->queryParams[self::PARAM_FIELDS] ?? [];

        return Fields::createFromStrings($fieldsData);
    }
}
