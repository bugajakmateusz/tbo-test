<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\ResourcesList;

use Tab\Packages\Faker\Faker;
use Tab\Packages\ResourcesList\Exception\ResourcesListException;
use Tab\Packages\ResourcesList\Page;
use Tab\Packages\ResourcesList\Pagination;
use Tab\Packages\ResourcesList\TotalItems;
use Tab\Packages\TestCase\UnitTestCase;

/** @internal */
final class PaginationTest extends UnitTestCase
{
    public function test_current_page(): void
    {
        // Arrange
        $pageNumber = Faker::int(1, 100);

        // Act
        $pagination = $this->createPagination(
            $pageNumber,
            1,
            $pageNumber,
        );

        // Assert
        self::assertSame($pageNumber, $pagination->current());
    }

    public function test_page_count(): void
    {
        // Arrange
        $pageSize = Faker::int(1, 5);
        $totalItems = Faker::int(1, 200);
        $expectedPageCount = (int) \ceil($totalItems / $pageSize);

        // Act
        $pagination = $this->createPagination(
            Faker::int(1, 100),
            $pageSize,
            $totalItems,
        );

        // Assert
        self::assertSame($expectedPageCount, $pagination->pageCount());
    }

    public function test_page_number_will_aligned_with_pages_count(): void
    {
        // Act
        $pagination = $this->createPagination(
            5,
            1,
            4,
        );

        // Assert
        self::assertSame(4, $pagination->pageCount());
    }

    /**
     * @dataProvider pagesInRangeProvider
     *
     * @param \Closure(): array{
     *     pageNumber: int,
     *     pageSize: int,
     *     totalItems: int,
     *     pageRange: int,
     *     expectedPagesInRange: int[],
     * } $createParams
     */
    public function test_pages_in_range(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'pageNumber' => $pageNumber,
            'pageSize' => $pageSize,
            'totalItems' => $totalItems,
            'pageRange' => $pageRange,
            'expectedPagesInRange' => $expectedPagesInRange,
        ] = $createParams();

        // Act
        $pagination = $this->createPagination(
            $pageNumber,
            $pageSize,
            $totalItems,
            $pageRange,
        );

        // Assert
        self::assertSame($expectedPagesInRange, $pagination->pagesInRange());
    }

    /**
     * @dataProvider startPageProvider
     *
     * @param \Closure(): array{
     *     pageNumber: int,
     *     pageSize: int,
     *     totalItems: int,
     *     pageRange: int,
     *     expectedStartPage: int,
     * } $createParams
     */
    public function test_start_page(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'pageNumber' => $pageNumber,
            'pageSize' => $pageSize,
            'totalItems' => $totalItems,
            'pageRange' => $pageRange,
            'expectedStartPage' => $expectedStartPage,
        ] = $createParams();

        // Act
        $pagination = $this->createPagination(
            $pageNumber,
            $pageSize,
            $totalItems,
            $pageRange,
        );

        // Assert
        self::assertSame($expectedStartPage, $pagination->startPage());
    }

    /**
     * @dataProvider endPageProvider
     *
     * @param \Closure(): array{
     *     pageNumber: int,
     *     pageSize: int,
     *     totalItems: int,
     *     pageRange: int,
     *     expectedEndPage: int,
     * } $createParams
     */
    public function test_end_page(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'pageNumber' => $pageNumber,
            'pageSize' => $pageSize,
            'totalItems' => $totalItems,
            'pageRange' => $pageRange,
            'expectedEndPage' => $expectedEndPage,
        ] = $createParams();

        // Act
        $pagination = $this->createPagination(
            $pageNumber,
            $pageSize,
            $totalItems,
            $pageRange,
        );

        // Assert
        self::assertSame($expectedEndPage, $pagination->endPage());
    }

    /**
     * @dataProvider hasPreviousPageProvider
     *
     * @param \Closure(): array{
     *     pageNumber: int,
     *     pageSize: int,
     *     totalItems: int,
     *     expectedHasPreviousPage: bool,
     * } $createParams
     */
    public function test_has_previous_page(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'pageNumber' => $pageNumber,
            'pageSize' => $pageSize,
            'totalItems' => $totalItems,
            'expectedHasPreviousPage' => $expectedHasPreviousPage,
        ] = $createParams();

        // Act
        $pagination = $this->createPagination(
            $pageNumber,
            $pageSize,
            $totalItems,
        );

        // Assert
        self::assertSame($expectedHasPreviousPage, $pagination->hasPreviousPage());
    }

    /**
     * @dataProvider hasNextPageProvider
     *
     * @param \Closure(): array{
     *     pageNumber: int,
     *     pageSize: int,
     *     totalItems: int,
     *     expectedHasNextPage: bool,
     * } $createParams
     */
    public function test_has_next_page(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'pageNumber' => $pageNumber,
            'pageSize' => $pageSize,
            'totalItems' => $totalItems,
            'expectedHasNextPage' => $expectedHasNextPage,
        ] = $createParams();

        // Act
        $pagination = $this->createPagination(
            $pageNumber,
            $pageSize,
            $totalItems,
        );

        // Assert
        self::assertSame($expectedHasNextPage, $pagination->hasNextPage());
    }

    public function test_get_previous_page(): void
    {
        // Act
        $pagination = $this->createPagination(
            2,
            1,
            2,
        );

        // Assert
        self::assertSame(1, $pagination->previous());
    }

    public function test_get_previous_throws_on_there_is_no_next_page(): void
    {
        // Arrange
        $pagination = $this->createPagination(
            1,
            1,
            2,
        );
        // Expect
        $this->expectException(ResourcesListException::class);
        $this->expectExceptionMessage('There is no previous page.');

        // Act
        $pagination->previous();
    }

    public function test_get_next_page(): void
    {
        // Act
        $pagination = $this->createPagination(
            1,
            1,
            2,
        );

        // Assert
        self::assertSame(2, $pagination->next());
    }

    public function test_get_next_throws_when_there_is_no_next_page(): void
    {
        // Arrange
        $pagination = $this->createPagination(
            2,
            1,
            2,
        );

        // Expect
        $this->expectException(ResourcesListException::class);
        $this->expectExceptionMessage('There is no next page.');

        // Act
        $pagination->next();
    }

    /** @return iterable<string,array{\Closure}> */
    public static function pagesInRangeProvider(): iterable
    {
        yield 'middle page' => [
            static fn (): array => [
                'pageNumber' => 10,
                'pageSize' => 1,
                'totalItems' => 20,
                'pageRange' => 5,
                'expectedPagesInRange' => [
                    8,
                    9,
                    10,
                    11,
                    12,
                ],
            ],
        ];

        yield 'first page' => [
            static fn (): array => [
                'pageNumber' => 1,
                'pageSize' => 1,
                'totalItems' => 20,
                'pageRange' => 5,
                'expectedPagesInRange' => [
                    1,
                    2,
                    3,
                    4,
                    5,
                ],
            ],
        ];

        yield 'last page' => [
            static fn (): array => [
                'pageNumber' => 20,
                'pageSize' => 1,
                'totalItems' => 20,
                'pageRange' => 5,
                'expectedPagesInRange' => [
                    16,
                    17,
                    18,
                    19,
                    20,
                ],
            ],
        ];

        yield 'last page align' => [
            static fn (): array => [
                'pageNumber' => 1,
                'pageSize' => 1,
                'totalItems' => 3,
                'pageRange' => 5,
                'expectedPagesInRange' => [
                    1,
                    2,
                    3,
                ],
            ],
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function startPageProvider(): iterable
    {
        yield 'first page' => [
            static fn (): array => [
                'pageNumber' => 1,
                'pageSize' => 1,
                'totalItems' => 20,
                'pageRange' => 5,
                'expectedStartPage' => 1,
            ],
        ];

        yield 'last page' => [
            static fn (): array => [
                'pageNumber' => 20,
                'pageSize' => 1,
                'totalItems' => 20,
                'pageRange' => 5,
                'expectedStartPage' => 16,
            ],
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function endPageProvider(): iterable
    {
        yield 'first page' => [
            static fn (): array => [
                'pageNumber' => 1,
                'pageSize' => 1,
                'totalItems' => 20,
                'pageRange' => 5,
                'expectedEndPage' => 5,
            ],
        ];

        yield 'last page' => [
            static fn (): array => [
                'pageNumber' => 20,
                'pageSize' => 1,
                'totalItems' => 20,
                'pageRange' => 5,
                'expectedEndPage' => 20,
            ],
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function hasPreviousPageProvider(): iterable
    {
        yield 'first page' => [
            static fn (): array => [
                'pageNumber' => 1,
                'pageSize' => 1,
                'totalItems' => 5,
                'expectedHasPreviousPage' => false,
            ],
        ];

        yield 'last page' => [
            static fn (): array => [
                'pageNumber' => 3,
                'pageSize' => 1,
                'totalItems' => 3,
                'expectedHasPreviousPage' => true,
            ],
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function hasNextPageProvider(): iterable
    {
        yield 'first page' => [
            static fn (): array => [
                'pageNumber' => 1,
                'pageSize' => 1,
                'totalItems' => 5,
                'expectedHasNextPage' => true,
            ],
        ];

        yield 'last page' => [
            static fn (): array => [
                'pageNumber' => 3,
                'pageSize' => 1,
                'totalItems' => 3,
                'expectedHasNextPage' => false,
            ],
        ];
    }

    private function createPagination(
        int $pageNumber,
        int $pageSize,
        int $totalItems,
        int $pageRange = Pagination::PAGE_RANGE,
    ): Pagination {
        $page = Page::fromArray(['size' => $pageSize, 'number' => $pageNumber]);
        $totalItems = TotalItems::fromInt($totalItems);

        return new Pagination(
            $page,
            $totalItems,
            $pageRange,
        );
    }
}
