<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\ResourcesList;

use Symfony\Component\HttpFoundation\Request;
use Tab\Packages\Faker\Faker;
use Tab\Packages\ResourcesList\Filter;
use Tab\Packages\ResourcesList\QueryParamsExtractorFactory;
use Tab\Packages\ResourcesList\Sort;
use Tab\Packages\TestCase\UnitTestCase;

/** @internal */
final class QueryParamsExtractorFactoryTest extends UnitTestCase
{
    public function test_query_params_can_be_created_from_symfony_request(): void
    {
        // Arrange
        $pageSize = 10;
        $pageNumber = 2;
        $includeProject = 'project.' . Faker::word();
        $includeStudent = 'student.' . Faker::word();
        $fieldType = Faker::word();
        $field1 = Faker::word();
        $field2 = Faker::word();
        $filterName = Faker::word();
        $filterValue = Faker::word();
        $sort1 = Faker::word();
        $sort2 = Faker::word();
        $request = Request::create(
            '/',
            parameters: [
                'fields' => [
                    $fieldType => "{$field1},{$field2}",
                ],
                'page' => [
                    'size' => $pageSize,
                    'number' => $pageNumber,
                ],
                'sort' => "{$sort1},-{$sort2}",
                'filter' => [
                    $filterName => $filterValue,
                ],
                'include' => "{$includeProject},{$includeStudent}",
            ],
        );
        $factory = new QueryParamsExtractorFactory();
        $expectedSorts = [
            [
                'name' => $sort1,
                'direction' => 'ASC',
            ],
            [
                'name' => $sort2,
                'direction' => 'DESC',
            ],
        ];
        $expectedIncludes = [
            $includeProject,
            $includeStudent,
        ];
        $expectedFields = [
            $fieldType => [
                $field1,
                $field2,
            ],
        ];
        $expectedFilters = [
            [
                'name' => $filterName,
                'value' => $filterValue,
            ],
        ];

        // Act
        $queryParamsExtractor = $factory->fromRequestQueryParams($request);

        // Assert
        $page = $queryParamsExtractor->page();
        self::assertSame($pageSize, $page->size());
        self::assertSame($pageNumber, $page->number());
        $fields = $queryParamsExtractor->fields();
        self::assertSame($expectedFields, $fields->toArray());
        $sorts = $queryParamsExtractor->sorts();
        self::assertSame(
            $expectedSorts,
            \array_map(
                static fn (Sort $sort): array => [
                    'name' => $sort->name(),
                    'direction' => $sort->direction(),
                ],
                $sorts->toArray(),
            ),
        );
        $filters = $queryParamsExtractor->filters();
        self::assertSame(
            $expectedFilters,
            \array_map(
                static fn (Filter $filter): array => [
                    'name' => $filter->name(),
                    'value' => $filter->value(),
                ],
                $filters->toArray(),
            ),
        );
        $includes = $queryParamsExtractor->includes();
        self::assertSame($expectedIncludes, $includes->toArray());
    }
}
