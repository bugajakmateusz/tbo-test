<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\SqlExpressions;

use Tab\Packages\SqlExpressions\JsonArrayAggregate;
use Tab\Packages\SqlExpressions\OrderBy;
use Tab\Packages\TestCase\UnitTestCase;

/** @internal */
final class JsonArrayAggregateTest extends UnitTestCase
{
    /** @dataProvider toStringProvider */
    public function test_json_array_aggregate_can_be_converted_to_string(\Closure $paramsGenerator): void
    {
        // Arrange
        /**
         * @var string       $attribute
         * @var bool         $distinct
         * @var null|OrderBy $orderBy
         */
        [
            'attribute' => $attribute,
            'distinct' => $distinct,
            'orderBy' => $orderBy,
            'expectedString' => $expectedString,
        ] = $paramsGenerator();
        $jsonArrayAggregate = new JsonArrayAggregate(
            $attribute,
            $distinct,
            $orderBy,
        );

        // Act
        $jsonArrayAggregateString = $jsonArrayAggregate->toString();

        // Assert
        self::assertSame($expectedString, $jsonArrayAggregateString);
    }

    /** @return iterable<string,array{\Closure}> */
    public static function toStringProvider(): iterable
    {
        yield 'only attribute' => [
            static fn (): array => [
                'attribute' => 'u.name',
                'distinct' => false,
                'orderBy' => null,
                'expectedString' => 'JSON_AGG(u.name)',
            ],
        ];

        yield 'distinct attribute' => [
            static fn (): array => [
                'attribute' => 'u.id',
                'distinct' => true,
                'orderBy' => null,
                'expectedString' => <<<'SQL'
                    CAST(
                        CONCAT(
                            '[',
                            GROUP_CONCAT(
                                DISTINCT u.id
                            ),
                            ']'
                        )
                        AS JSON
                    )
                    SQL,
            ],
        ];

        yield 'order by and attribute' => [
            static function (): array {
                $orderBy = new OrderBy();
                $orderBy->add('p.id', OrderBy::DIRECTION_DESC);

                return [
                    'attribute' => 'p.id',
                    'distinct' => false,
                    'orderBy' => $orderBy,
                    'expectedString' => <<<'SQL'
                        CAST(
                            CONCAT(
                                '[',
                                GROUP_CONCAT(
                                    p.id ORDER BY p.id DESC
                                ),
                                ']'
                            )
                            AS JSON
                        )
                        SQL,
                ];
            },
        ];

        yield 'order by, distinct and attribute' => [
            static function (): array {
                $orderBy = new OrderBy();
                $orderBy->add('m.created_at', OrderBy::DIRECTION_ASC);

                return [
                    'attribute' => 'm.id',
                    'distinct' => true,
                    'orderBy' => $orderBy,
                    'expectedString' => <<<'SQL'
                        CAST(
                            CONCAT(
                                '[',
                                GROUP_CONCAT(
                                    DISTINCT m.id ORDER BY m.created_at ASC
                                ),
                                ']'
                            )
                            AS JSON
                        )
                        SQL,
                ];
            },
        ];
    }
}
