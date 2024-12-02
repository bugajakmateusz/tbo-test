<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\ResourcesList;

use Tab\Packages\ResourcesList\Filter;
use Tab\Packages\ResourcesList\Filters;
use Tab\Packages\ResourcesList\Filters\FiltersException;
use Tab\Packages\TestCase\UnitTestCase;

/** @internal */
final class FiltersTest extends UnitTestCase
{
    public function test_providing_not_allowed_filter_will_throw_exception(): void
    {
        $this->expectException(FiltersException::class);
        $this->expectExceptionMessage("Unsupported filters found: 'school', try one of these: 'user'.");

        $filters = Filters::fromArray(['school' => 15, 'user' => 174]);
        $filters->checkSupportedFilters('user');
    }

    public function test_has_filter(): void
    {
        $filters = Filters::fromArray(['name' => 'Some name']);

        self::assertTrue($filters->has('name'));
        self::assertFalse($filters->has('userId'));
    }

    public function test_get_throws_exception_when_filter_not_exists(): void
    {
        $filterName = 'userId';
        $this->expectException(FiltersException::class);
        $this->expectExceptionMessage("Filter with name '{$filterName}' not found.");

        $filters = Filters::fromArray([]);
        $filters->get($filterName);
    }

    public function test_filter_can_be_fetched_by_name(): void
    {
        $filterName = 'stageName';
        $filterValue = 'planning';

        $filters = Filters::fromArray([$filterName => $filterValue]);
        $filter = $filters->get($filterName);

        self::assertSame($filterValue, $filter->value());
    }

    public function test_non_empty_filters_are_returned(): void
    {
        $filters = Filters::fromArray(
            [
                'emptyString' => '',
                'nullValue' => null,
                'emptyArray' => [],
            ],
        );

        self::assertCount(0, $filters->nonEmptyFilters());
    }

    public function test_zero_is_non_empty_filter(): void
    {
        $filters = Filters::fromArray(
            [
                'zeroString' => '0',
                'zeroInt' => 0,
            ],
        );

        self::assertCount(2, $filters->nonEmptyFilters());
    }

    public function test_filters_can_be_created_from_filters(): void
    {
        $filters = Filters::fromFilters(new Filter('user', '1'));

        self::assertCount(1, $filters->toArray());
    }
}
