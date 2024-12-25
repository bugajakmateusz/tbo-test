<?php

declare(strict_types=1);

namespace Polsl\Packages\Tests\ResourcesList\Fields;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\ResourcesList\Fields\TypeFields;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class TypeFieldsTest extends UnitTestCase
{
    public function test_type_fields_can_be_created(): void
    {
        $type = Faker::words(1);
        $field1 = Faker::words(5);
        $field2 = Faker::words(5);
        $fields = [$field1];
        $typeFields = TypeFields::create($type, $fields);

        self::assertFalse($typeFields->isAllFields());
        self::assertSame($type, $typeFields->type());
        self::assertSame($fields, $typeFields->fields());
        self::assertTrue($typeFields->hasField($field1));
        self::assertFalse($typeFields->hasField($field2));
    }

    public function test_all_fields_can_be_created(): void
    {
        $type = Faker::words(1);
        $typeFields = TypeFields::createAllFields($type);

        self::assertTrue($typeFields->isAllFields());
        self::assertSame($type, $typeFields->type());
        self::assertSame([], $typeFields->fields());
        for ($i = 0; $i < 10; ++$i) {
            self::assertTrue(
                $typeFields->hasField(
                    Faker::words(3),
                ),
            );
        }
    }

    /**
     * @dataProvider emptyNameProvider
     *
     * @param \Closure(): array{
     *     constructorName: string,
     *     constructorParams: string,
     * } $createParams
     */
    public function test_empty_type_is_not_allowed(\Closure $createParams): void
    {
        [
            'constructorName' => $constructorName,
            'constructorParams' => $constructorParams,
        ] = $createParams();
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Empty type is not allowed.');

        TypeFields::{$constructorName}(...$constructorParams);
    }

    /** @return iterable<string,array{\Closure}> */
    public static function emptyNameProvider(): iterable
    {
        yield 'create' => [
            static fn (): array => [
                'constructorName' => 'create',
                'constructorParams' => [
                    '',
                    [],
                ],
            ],
        ];
        yield 'create all fields' => [
            static fn (): array => [
                'constructorName' => 'createAllFields',
                'constructorParams' => [''],
            ],
        ];
    }
}
