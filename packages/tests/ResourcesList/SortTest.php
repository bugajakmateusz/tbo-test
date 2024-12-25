<?php

declare(strict_types=1);

namespace Polsl\Packages\Tests\ResourcesList;

use Polsl\Packages\ResourcesList\Sort;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class SortTest extends UnitTestCase
{
    /**
     * @param \Closure(): array{
     *     sort: string,
     *     expectedName: string,
     *     expectedDirection: string,
     * } $createParams
     *
     * @dataProvider jsonApiSortProvider
     */
    public function test_sort_can_be_created_from_json_api_string(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'sort' => $sort,
            'expectedName' => $expectedName,
            'expectedDirection' => $expectedDirection,
        ] = $createParams();

        // Act
        $sort = Sort::fromJsonApiString($sort);

        // Assert
        self::assertSame($expectedName, $sort->name());
        self::assertSame($expectedDirection, $sort->direction());
    }

    /**
     * @param \Closure(): string $sortGenerator
     *
     * @dataProvider jsonApiEmptySortProvider
     */
    public function test_json_api_sort_cannot_be_empty(\Closure $sortGenerator): void
    {
        // Arrange
        $sort = $sortGenerator();

        // Expect
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Sort cannot be empty.');

        // Assert
        Sort::fromJsonApiString($sort);
    }

    /** @return iterable<string,array{\Closure}> */
    public static function jsonApiSortProvider(): iterable
    {
        yield 'name autocomplete asc' => [
            static fn (): array => [
                'sort' => 'nameAutocomplete',
                'expectedName' => 'nameAutocomplete',
                'expectedDirection' => Sort::SORT_DIRECTION_ASC,
            ],
        ];
        yield 'name autocomplete desc' => [
            static fn (): array => [
                'sort' => '-nameAutocomplete',
                'expectedName' => 'nameAutocomplete',
                'expectedDirection' => Sort::SORT_DIRECTION_DESC,
            ],
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function jsonApiEmptySortProvider(): iterable
    {
        yield 'empty string' => [
            static fn (): string => '',
        ];
        yield 'only minus sign' => [
            static fn (): string => '-',
        ];
    }
}
