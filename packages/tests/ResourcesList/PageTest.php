<?php

declare(strict_types=1);

namespace Polsl\Packages\Tests\ResourcesList;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\ResourcesList\Page;
use Polsl\Packages\ResourcesList\PageException;
use Polsl\Packages\TestCase\UnitTestCase;

/**
 * @phpstan-type PageConstructor \Closure(int $number, int $size): Page
 *
 * @internal
 */
final class PageTest extends UnitTestCase
{
    public function test_default_page_values_with_empty_array(): void
    {
        $page = Page::fromArray([]);

        self::assertSame(Page::DEFAULT_NUMBER, $page->number());
        self::assertSame(Page::DEFAULT_SIZE, $page->size());
    }

    /**
     * @param PageConstructor $pageConstructor
     *
     * @dataProvider namedConstructorProvider
     */
    public function test_offset_can_be_calculated(\Closure $pageConstructor): void
    {
        // Arrange
        $number = Faker::int(1, 999);
        $size = Faker::randomElement(Page::SIZES);
        $expectedOffset = ($number - 1) * $size;
        $page = $pageConstructor($number, $size);

        // Act
        $offset = $page->offset();

        // Assert
        self::assertSame($expectedOffset, $offset);
    }

    public function test_page_can_be_constructed_from_array(): void
    {
        $number = Faker::int(1, 999);
        $size = Faker::randomElement(Page::SIZES);
        $page = Page::fromArray(['size' => $size, 'number' => $number]);

        self::assertSame($page->number(), $number);
    }

    /**
     * @param PageConstructor $pageConstructor
     *
     * @dataProvider namedConstructorProvider
     */
    public function test_page_number_cannot_be_lower_than_one(\Closure $pageConstructor): void
    {
        // Arrange
        $number = Faker::int(-9999, 0);
        $size = Faker::randomElement(Page::SIZES);

        // Expect
        $this->expectException(PageException::class);
        $this->expectExceptionMessage("Page number cannot be lower than '1', '{$number}' passed.");

        // Act
        $pageConstructor($number, $size);
    }

    /**
     * @param PageConstructor $pageConstructor
     *
     * @dataProvider namedConstructorProvider
     */
    public function test_page_size_below_one_is_not_accepted(\Closure $pageConstructor): void
    {
        // Arrange
        $pageSize = Faker::int(-9999, 0);

        // Expect
        $this->expectException(PageException::class);
        $this->expectExceptionMessage("Page size cannot be lower than '1', '{$pageSize}' passed.");

        // Act
        $pageConstructor(1, $pageSize);
    }

    /**
     * @param PageConstructor $pageConstructor
     *
     * @dataProvider namedConstructorProvider
     */
    public function test_page_number_cannot_be_other_than_declared(\Closure $pageConstructor): void
    {
        // Arrange
        $number = Faker::int(1, 9999);
        $size = Faker::int(101, 9999);

        // Expect
        $this->expectException(PageException::class);
        $this->expectExceptionMessage(
            "Page size '{$size}' is not allowed, try one of these: '1-5', '10', '15', '20', '25', '50', '100'.",
        );

        // Act
        $pageConstructor($number, $size);
    }

    /**
     * @param PageConstructor $pageConstructor
     *
     * @dataProvider thresholdNumbersProvider
     */
    public function test_any_number_below_threshold_is_accepted_page_size(
        int $pageSize,
        \Closure $pageConstructor,
    ): void {
        // Act
        $page = $pageConstructor(1, $pageSize);

        // Assert
        self::assertSame($pageSize, $page->size());
    }

    /**
     * @param PageConstructor $pageConstructor
     *
     * @dataProvider namedConstructorProvider
     */
    public function test_above_threshold_number_is_not_accepted(\Closure $pageConstructor): void
    {
        // Arrange
        $pageSize = Faker::int(6, 9);

        // Expect
        $this->expectException(PageException::class);
        $this->expectExceptionMessage(
            "Page size '{$pageSize}' is not allowed, try one of these: '1-5', '10', '15', '20', '25', '50', '100'.",
        );

        // Act
        $pageConstructor(1, $pageSize);
    }

    /** @return iterable<string,array<int, \Closure|int>> */
    public static function thresholdNumbersProvider(): iterable
    {
        foreach (self::namedConstructorProvider() as $name => $pageConstructor) {
            foreach (\range(1, 5) as $size) {
                yield "{$name}-{$size}" => \array_merge(
                    [$size],
                    $pageConstructor,
                );
            }
        }
    }

    /** @return iterable<string, array{\Closure}> */
    public static function namedConstructorProvider(): iterable
    {
        yield 'array' => [
            static fn (int $number, int $size): Page => Page::fromArray(
                [
                    'size' => $size,
                    'number' => $number,
                ],
            ),
        ];

        yield 'scalars' => [
            static fn (int $number, int $size): Page => Page::fromScalars($number, $size),
        ];
    }
}
