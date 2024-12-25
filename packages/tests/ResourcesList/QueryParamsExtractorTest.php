<?php

declare(strict_types=1);

namespace Polsl\Packages\Tests\ResourcesList;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\ResourcesList\Filter;
use Polsl\Packages\ResourcesList\QueryParamsExtractor;
use Polsl\Packages\ResourcesList\Sort;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class QueryParamsExtractorTest extends UnitTestCase
{
    public function test_query_params_can_be_extracted(): void
    {
        $queryParams = [
            'sort' => '-id',
            'filter' => [
                'name' => 'Abc',
            ],
            'include' => 'avatar',
            'page' => [
                'size' => 5,
                'number' => 2,
            ],
        ];
        $queryParamsExtractor = new QueryParamsExtractor($queryParams);

        $filters = $queryParamsExtractor->filters();
        $sorts = $queryParamsExtractor->sorts();
        $includes = $queryParamsExtractor->includes();
        $page = $queryParamsExtractor->page();

        $expectedFilter = new Filter('name', 'Abc');
        $expectedSort = Sort::fromJsonApiString('-id');

        self::assertEquals([$expectedFilter], $filters->toArray());
        self::assertEquals([$expectedSort], $sorts->toArray());
        self::assertEquals(['avatar'], $includes->toArray());
        self::assertSame(5, $page->size());
        self::assertSame(2, $page->number());
    }

    public function test_extracting_fields(): void
    {
        // Arrange
        $type = Faker::word();
        $field1 = Faker::word();
        $field2 = Faker::word();
        $queryParams = [
            'fields' => [
                $type => "{$field1},{$field2}",
            ],
        ];
        $queryParamsExtractor = new QueryParamsExtractor($queryParams);

        // Act
        $fields = $queryParamsExtractor->fields();

        // Assert
        self::assertSame(
            [
                $type => [
                    $field1,
                    $field2,
                ],
            ],
            $fields->toArray(),
        );
    }
}
