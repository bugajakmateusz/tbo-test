<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\ResourcesList;

use Tab\Packages\Faker\Faker;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\TestCase\UnitTestCase;

/** @internal */
final class FieldsTest extends UnitTestCase
{
    public function test_type_fields_get_fail_on_missing_type(): void
    {
        // Arrange
        $missingType = Faker::words(5);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Unable to find fields for type '{$missingType}'.");
        $type = Faker::words(5);

        // Act
        $fields = Fields::createFromArray(
            [
                Fields\TypeFields::createAllFields($type),
            ],
        );

        // Assert
        $fields->typeFields($missingType);
    }

    public function test_to_array_can_ignore_all_fields_type(): void
    {
        $type1 = Faker::words(5);
        $type2 = Faker::words(5);
        $field1 = Faker::words(1);
        $fields = Fields::createFromArray(
            [
                Fields\TypeFields::create($type1, [$field1]),
                Fields\TypeFields::createAllFields($type2),
            ],
        );

        self::assertSame(
            [$type1 => [$field1]],
            $fields->toArray(true),
        );
    }

    /**
     * @dataProvider arrayFieldsProvider
     *
     * @param \Closure(): array{
     *     data: array<string,string[]>|Fields\TypeFields[],
     *     expectedData: array<string,string[]>,
     * } $createParams
     */
    public function test_fields_can_be_created_from_array(\Closure $createParams): void
    {
        // Arrange
        [
            'data' => $data,
            'expectedData' => $expectedData,
        ] = $createParams();

        // Act
        $fields = Fields::createFromArray($data);

        // Assert
        self::assertSame($expectedData, $fields->toArray());
    }

    public function test_create_all_fields_for_type_named_constructor(): void
    {
        // Arrange
        $type = Faker::words(1);

        // Act
        $fields = Fields::createAllFieldsForType($type);

        // Assert
        self::assertSame(
            [$type => []],
            $fields->toArray(false),
        );
    }

    public function test_create_fields_for_type_named_constructor(): void
    {
        // Arrange
        $type = Faker::words(1);
        $fieldsList = [Faker::words(1), Faker::words(1)];

        // Act
        $fields = Fields::createFieldsForType($type, ...$fieldsList);

        // Assert
        self::assertSame(
            [$type => $fieldsList],
            $fields->toArray(),
        );
    }

    /** @dataProvider isEmptyProvider */
    public function test_is_empty(\Closure $paramsGenerator): void
    {
        // Arrange
        /**
         * @var Fields $fields
         * @var bool   $expectedIsEmpty
         */
        [
            'fields' => $fields,
            'expectedIsEmpty' => $expectedIsEmpty,
        ] = $paramsGenerator();

        // Act
        $isEmpty = $fields->isEmpty();

        // Assert
        self::assertSame($expectedIsEmpty, $isEmpty);
    }

    /** @dataProvider createFromStringProvider */
    public function test_creating_fields_from_string(\Closure $paramsGenerator): void
    {
        // Arrange
        /**
         * @var array<string, string>   $fieldsData
         * @var array<string, string[]> $expectedFields
         */
        [
            'fieldsData' => $fieldsData,
            'expectedFields' => $expectedFields,
        ] = $paramsGenerator();

        // Act
        $fields = Fields::createFromStrings($fieldsData);

        // Assert
        self::assertSame($expectedFields, $fields->toArray());
    }

    /** @dataProvider hasTypeProvider */
    public function test_has_type(\Closure $paramsGenerator): void
    {
        // Arrange
        /**
         * @var string $type
         * @var Fields $fields
         * @var bool   $expectedHasType
         */
        [
            'type' => $type,
            'fields' => $fields,
            'expectedHasType' => $expectedHasType,
        ] = $paramsGenerator();

        // Act
        $hasType = $fields->hasType($type);

        // Assert
        self::assertSame($expectedHasType, $hasType);
    }

    /** @return iterable<string,array{\Closure}> */
    public static function arrayFieldsProvider(): iterable
    {
        yield 'only array' => [
            static function (): array {
                $array = [
                    Faker::words(5) => [Faker::words(1)],
                    Faker::words(5) => [Faker::words(1)],
                ];

                return [
                    'data' => $array,
                    'expectedData' => $array,
                ];
            },
        ];

        yield 'mixed array' => [
            static function (): array {
                $type1 = Faker::words(5);
                $type2 = Faker::words(5);
                $field1 = Faker::words(1);
                $field2 = Faker::words(1);
                $arrayResult = [
                    $type1 => [$field1],
                    $type2 => [$field2],
                ];

                return [
                    'data' => [
                        $type1 => [$field1],
                        Fields\TypeFields::create($type2, [$field2]),
                    ],
                    'expectedData' => $arrayResult,
                ];
            },
        ];

        yield 'only objects' => [
            static function (): array {
                $type1 = Faker::words(5);
                $type2 = Faker::words(5);
                $field1 = Faker::words(1);
                $field2 = Faker::words(1);
                $arrayResult = [
                    $type1 => [$field1],
                    $type2 => [$field2],
                ];

                return [
                    'data' => [
                        Fields\TypeFields::create($type1, [$field1]),
                        Fields\TypeFields::create($type2, [$field2]),
                    ],
                    'expectedData' => $arrayResult,
                ];
            },
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function isEmptyProvider(): iterable
    {
        yield 'empty' => [
            static fn (): array => [
                'fields' => Fields::createFromArray([]),
                'expectedIsEmpty' => true,
            ],
        ];

        yield 'not empty' => [
            static fn (): array => [
                'fields' => Fields::createFromArray(
                    [
                        Faker::word() => [
                            Faker::word(),
                        ],
                    ],
                ),
                'expectedIsEmpty' => false,
            ],
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function createFromStringProvider(): iterable
    {
        yield 'empty' => [
            static fn (): array => [
                'fieldsData' => [],
                'expectedFields' => [],
            ],
        ];

        yield 'not empty' => [
            static function (): array {
                $type = Faker::word();
                $field1 = Faker::word();
                $field2 = Faker::word();

                return [
                    'fieldsData' => [
                        $type => "{$field1},{$field2}",
                    ],
                    'expectedFields' => [
                        $type => [
                            $field1,
                            $field2,
                        ],
                    ],
                ];
            },
        ];
    }

    /** @return iterable<string, array{\Closure}> */
    public static function hasTypeProvider(): iterable
    {
        yield 'has type' => [
            static function (): array {
                $type = Faker::word();

                return [
                    'type' => $type,
                    'fields' => Fields::createFromArray(
                        [
                            $type => [
                                Faker::word(),
                            ],
                        ],
                    ),
                    'expectedHasType' => true,
                ];
            },
        ];

        yield 'has not type' => [
            static function (): array {
                $type = Faker::word();

                return [
                    'type' => $type . Faker::word(),
                    'fields' => Fields::createFromArray(
                        [
                            $type => [
                                Faker::word(),
                            ],
                        ],
                    ),
                    'expectedHasType' => false,
                ];
            },
        ];
    }
}
