<?php

declare(strict_types=1);

namespace Polsl\Packages\Tests\SqlExpressions;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\SqlExpressions\OrderBy;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class OrderByTest extends UnitTestCase
{
    /**
     * @param \Closure(): array{
     *     column: string,
     *     direction: string,
     *     expectedExceptionMessage: string,
     * } $createParams
     *
     * @dataProvider wrongOrderProvider
     */
    public function test_wrong_order_is_not_accepted(
        \Closure $createParams,
    ): void {
        [
            'column' => $column,
            'direction' => $direction,
            'expectedExceptionMessage' => $expectedExceptionMessage,
        ] = $createParams();
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $orderBy = new OrderBy();
        $orderBy->add($column, $direction);
    }

    public function test_to_string_is_allowed_with_at_least_one_item(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('At least one order is required.');

        (new OrderBy())->toString();
    }

    public function test_order_by_can_be_converted_to_string(): void
    {
        $direction1 = Faker::randomElement([OrderBy::DIRECTION_ASC, OrderBy::DIRECTION_DESC]);
        $column1 = Faker::words(1);
        $direction2 = Faker::randomElement([OrderBy::DIRECTION_ASC, OrderBy::DIRECTION_DESC]);
        $column2 = Faker::words(2);

        $orderBy = new OrderBy();
        $orderBy
            ->add($column1, $direction1)
            ->add($column2, $direction2)
        ;

        self::assertSame(
            "ORDER BY {$column1} {$direction1}, {$column2} {$direction2}",
            $orderBy->toString(),
        );
    }

    public function test_passing_same_key_twice_produces_one_column(): void
    {
        $direction1 = Faker::randomElement([OrderBy::DIRECTION_ASC, OrderBy::DIRECTION_DESC]);
        $column1 = Faker::words(1);
        $direction2 = Faker::randomElement([OrderBy::DIRECTION_ASC, OrderBy::DIRECTION_DESC]);

        $orderBy = new OrderBy();
        $orderBy
            ->add($column1, $direction1)
            ->add($column1, $direction2)
        ;

        self::assertSame(
            "ORDER BY {$column1} {$direction2}",
            $orderBy->toString(),
        );
    }

    /** @return iterable<string,array{\Closure}> */
    public static function wrongOrderProvider(): iterable
    {
        yield 'empty column' => [
            static fn (): array => [
                'column' => '',
                'direction' => Faker::randomElement([OrderBy::DIRECTION_ASC, OrderBy::DIRECTION_DESC]),
                'expectedExceptionMessage' => 'Column cannot be empty.',
            ],
        ];

        yield 'empty direction' => [
            static fn (): array => [
                'column' => Faker::words(1),
                'direction' => '',
                'expectedExceptionMessage' => 'Direction cannot be empty.',
            ],
        ];

        yield 'wrong direction' => [
            static function (): array {
                $direction = Faker::words(1);
                $expectedExceptionMessage = "Direction '{$direction}' is invalid, try one of: 'ASC', DESC'.";

                return [
                    'column' => Faker::words(1),
                    'direction' => $direction,
                    'expectedExceptionMessage' => $expectedExceptionMessage,
                ];
            },
        ];
    }
}
