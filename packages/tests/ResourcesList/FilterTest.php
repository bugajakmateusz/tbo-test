<?php

declare(strict_types=1);

namespace Polsl\Packages\Tests\ResourcesList;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\ResourcesList\Filter;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class FilterTest extends UnitTestCase
{
    /**
     * @dataProvider arrayValueProvider
     *
     * @param \Closure(): array{
     *     value: int|string|string[],
     *     expectedValue: int[]|string[],
     * } $createParams
     */
    public function test_value_can_be_converted_to_array(\Closure $createParams): void
    {
        // Arrange
        [
            'value' => $value,
            'expectedValue' => $expectedValue,
        ] = $createParams();

        // Act
        $filter = new Filter('name', $value);

        // Assert
        self::assertSame($expectedValue, $filter->arrayValue());
    }

    /**
     * @dataProvider stringValueProvider
     *
     * @param \Closure(): array{
     *     value: int|string,
     *     expectedValue: int|string,
     *     sanitizers: string[],
     *     formatters: string[],
     * } $createParams
     */
    public function test_filter_value_can_be_converted_to_string(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'value' => $value,
            'expectedValue' => $expectedValue,
            'sanitizers' => $sanitizers,
            'formatters' => $formatters,
        ] = $createParams();

        // Act
        $filter = new Filter('test', $value);

        // Assert
        self::assertSame($expectedValue, $filter->stringValue($sanitizers, $formatters));
    }

    /**
     * @dataProvider intValueProvider
     *
     * @param \Closure(): array{
     *     value: string,
     *     expectedValue: int,
     *     absolute: bool,
     * } $createParams
     */
    public function test_int_value(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'value' => $value,
            'expectedValue' => $expectedValue,
            'absolute' => $absolute,
        ] = $createParams();

        // Act
        $filter = new Filter('id', $value);

        // Assert
        self::assertSame($expectedValue, $filter->intValue($absolute));
    }

    public function test_string_value_from_array_fail(): void
    {
        // Arrange
        $filter = new Filter('names', []);

        // Expect
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to get string from array value.');

        // Act
        $filter->stringValue();
    }

    /** @dataProvider boolValueProvider */
    public function test_bool_value(\Closure $paramsGenerator): void
    {
        // Arrange
        /**
         * @var null|array<int|string, mixed>|int|string $value
         * @var bool                                     $expectedBoolValue
         */
        [
            'value' => $value,
            'expectedBoolValue' => $expectedBoolValue,
        ] = $paramsGenerator();
        $name = Faker::word();
        $filter = new Filter($name, $value);

        // Act
        $boolValue = $filter->boolValue();

        // Assert
        self::assertSame($expectedBoolValue, $boolValue);
    }

    /** @return iterable<string,array{\Closure}> */
    public static function arrayValueProvider(): iterable
    {
        yield 'int' => [
            static function (): array {
                $value = Faker::int();

                return [
                    'value' => $value,
                    'expectedValue' => [$value],
                ];
            },
        ];

        yield 'string' => [
            static function (): array {
                $value = Faker::word();

                return [
                    'value' => $value,
                    'expectedValue' => [$value],
                ];
            },
        ];

        yield 'array' => [
            static function (): array {
                $value = Faker::wordsArray();

                return [
                    'value' => $value,
                    'expectedValue' => $value,
                ];
            },
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function stringValueProvider(): iterable
    {
        yield 'string' => [
            static function (): array {
                $value = Faker::word();

                return [
                    'value' => $value,
                    'expectedValue' => $value,
                    'sanitizers' => [],
                    'formatters' => [],
                ];
            },
        ];

        yield 'int' => [
            static function (): array {
                $value = Faker::stringNumberId();

                return [
                    'value' => (int) $value,
                    'expectedValue' => $value,
                    'sanitizers' => [],
                    'formatters' => [],
                ];
            },
        ];

        yield 'lower string' => [
            static fn (): array => [
                'value' => 'Łódź',
                'expectedValue' => 'łódź',
                'sanitizers' => [],
                'formatters' => [Filter::FORMATTER_LOWER],
            ],
        ];

        yield 'trim string' => [
            static function (): array {
                $value = Faker::word();

                return [
                    'value' => "    {$value}    ",
                    'expectedValue' => $value,
                    'sanitizers' => [],
                    'formatters' => [Filter::FORMATTER_TRIM],
                ];
            },
        ];

        yield 'trim and lower string' => [
            static function (): array {
                $value = Faker::word();

                return [
                    'value' => "    {$value}    ",
                    'expectedValue' => \mb_strtolower($value),
                    'sanitizers' => [],
                    'formatters' => [Filter::FORMATTER_LOWER, Filter::FORMATTER_TRIM],
                ];
            },
        ];

        yield 'sanitize string' => [
            static fn (): array => [
                'value' => '<script>alert()</script>',
                'expectedValue' => 'alert()',
                'sanitizers' => [Filter::SANITIZER_STRING],
                'formatters' => [],
            ],
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function intValueProvider(): iterable
    {
        yield 'only digits' => [
            static fn (): array => [
                'value' => '12 65',
                'expectedValue' => 1265,
                'absolute' => true,
            ],
        ];

        yield 'digits and characters' => [
            static fn (): array => [
                'value' => '3v4l',
                'expectedValue' => 34,
                'absolute' => true,
            ],
        ];

        yield 'digits and whitespaces' => [
            static fn (): array => [
                'value' => '\t1 2\r\n',
                'expectedValue' => 12,
                'absolute' => true,
            ],
        ];

        yield 'plus digits' => [
            static fn (): array => [
                'value' => '+1 86',
                'expectedValue' => 186,
                'absolute' => true,
            ],
        ];

        yield 'minus digits' => [
            static fn (): array => [
                'value' => '- 67',
                'expectedValue' => -67,
                'absolute' => false,
            ],
        ];

        yield 'minus digits absolute' => [
            static fn (): array => [
                'value' => '- 96576',
                'expectedValue' => 96576,
                'absolute' => true,
            ],
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function boolValueProvider(): iterable
    {
        yield 'empty string' => [
            static fn (): array => [
                'value' => '',
                'expectedBoolValue' => false,
            ],
        ];

        yield 'non-empty string' => [
            static fn (): array => [
                'value' => Faker::word(),
                'expectedBoolValue' => false,
            ],
        ];

        yield 'one as string' => [
            static fn (): array => [
                'value' => '1',
                'expectedBoolValue' => true,
            ],
        ];

        yield 'on text' => [
            static fn (): array => [
                'value' => 'on',
                'expectedBoolValue' => true,
            ],
        ];

        yield 'zero as string' => [
            static fn (): array => [
                'value' => '0',
                'expectedBoolValue' => false,
            ],
        ];

        yield 'true as string' => [
            static fn (): array => [
                'value' => 'true',
                'expectedBoolValue' => true,
            ],
        ];

        yield 'empty array' => [
            static fn (): array => [
                'value' => [],
                'expectedBoolValue' => false,
            ],
        ];

        yield 'non-empty array' => [
            static fn (): array => [
                'value' => [Faker::word()],
                'expectedBoolValue' => false,
            ],
        ];

        yield 'int zero' => [
            static fn (): array => [
                'value' => 0,
                'expectedBoolValue' => false,
            ],
        ];

        yield 'int one' => [
            static fn (): array => [
                'value' => 1,
                'expectedBoolValue' => true,
            ],
        ];

        yield 'null' => [
            static fn (): array => [
                'value' => null,
                'expectedBoolValue' => false,
            ],
        ];
    }
}
