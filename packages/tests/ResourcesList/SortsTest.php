<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\ResourcesList;

use Tab\Packages\ResourcesList\Sort;
use Tab\Packages\ResourcesList\Sorts;
use Tab\Packages\TestCase\UnitTestCase;

/** @internal */
final class SortsTest extends UnitTestCase
{
    public function test_sorts_can_be_created_from_valid_json_api_sort_string(): void
    {
        $jsonApiSortString = 'id,-name';
        $sorts = Sorts::fromJsonApiString($jsonApiSortString);

        $sortsString = \array_map(
            static fn (Sort $item): string => \strtolower("{$item->name()}:{$item->direction()}"),
            $sorts->toArray(),
        );
        $sortsString = \implode('|', $sortsString);

        self::assertSame('id:asc|name:desc', $sortsString);
    }

    public function test_check_not_allowed_sorts_throws_exception(): void
    {
        $jsonApiSortString = '-createdAt,updatedAt';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Unsupported sorts found: 'createdAt', try one of these: 'updatedAt'");

        $sorts = Sorts::fromJsonApiString($jsonApiSortString);
        $sorts->checkSupportedSorts('updatedAt');
    }

    public function test_sorts_can_be_created_with_empty_string(): void
    {
        $sorts = Sorts::fromJsonApiString('');

        self::assertCount(0, $sorts->toArray());
    }

    public function test_sorts_can_check_sort_existence(): void
    {
        $sorts = Sorts::fromJsonApiString('id');

        self::assertTrue($sorts->has('id'));
        self::assertFalse($sorts->has('name'));
    }
}
