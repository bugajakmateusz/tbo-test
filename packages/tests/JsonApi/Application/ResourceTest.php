<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\JsonApi\Application;

use Tab\Packages\Faker\Faker;
use Tab\Packages\JsonApi\Application\Exception\ResourceException;
use Tab\Packages\JsonApi\Application\JsonApiKeywords;
use Tab\Packages\JsonApi\Application\Resource as JsonApiResource;
use Tab\Packages\JsonApi\Application\ResourceIdentifier;
use Tab\Packages\TestCase\UnitTestCase;

/** @internal */
final class ResourceTest extends UnitTestCase
{
    public function test_resource_type_cannot_be_empty(): void
    {
        $this->expectException(ResourceException::class);
        $this->expectExceptionMessage("Non-empty 'type' is required.");

        $this->createResource('', '123');
    }

    public function test_resource_id_cannot_be_empty_while_getting_it(): void
    {
        $this->expectException(ResourceException::class);
        $this->expectExceptionMessage("Non-empty 'id' is required.");

        $resource = $this->createResource('tests');
        $resource->id();
    }

    public function test_resource_id_can_be_empty(): void
    {
        $resource = $this->createResource('tests');

        self::assertNotNull($resource);
    }

    public function test_resource_can_be_created_from_array(): void
    {
        $type = Faker::word();
        $id = '64223';
        $attributes = ['active' => true];
        $relationshipType = Faker::word();
        $relationshipsData = [
            'machine' => [
                JsonApiKeywords::DATA => [
                    JsonApiKeywords::ID => '1236',
                    JsonApiKeywords::TYPE => $relationshipType,
                ],
            ],
        ];
        $resource = $this->createResource(
            $type,
            $id,
            $attributes,
            $relationshipsData,
        );
        $relationships = $resource->relationships();
        /** @var ResourceIdentifier $machineRelationship */
        $machineRelationship = $relationships->relationship('machine');

        self::assertSame($type, $resource->type());
        self::assertSame($id, $resource->id());
        self::assertSame($attributes, $resource->attributes());
        self::assertSame('1236', $machineRelationship->id());
        self::assertSame($relationshipType, $machineRelationship->type());
    }

    public function test_check_expected_type_and_do_nothing_on_good_data(): void
    {
        // Arrange
        $type = Faker::words(1);
        $id = Faker::stringNumberId();
        $resource = $this->createResource($type, $id);

        // Expect
        $this->expectNotToPerformAssertions();

        // Act
        $resource->checkExpectedTypeAndId($id, $type);
    }

    /**
     * @param \Closure(): array{
     *     type: string,
     *     id: string,
     *     testedType: string,
     *     testedId: string,
     *     expectedExceptionMessage: string,
     * } $createParams
     *
     * @dataProvider wrongExpectedTypeAndIdProvider
     */
    public function test_check_expected_type_and_id_fails_on_wrong_data(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'type' => $type,
            'id' => $id,
            'testedType' => $testedType,
            'testedId' => $testedId,
            'expectedExceptionMessage' => $expectedExceptionMessage,
        ] = $createParams();
        $resource = $this->createResource($type, $id);

        // Expect
        $this->expectException(ResourceException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        // Act
        $resource->checkExpectedTypeAndId($testedId, $testedType);
    }

    public function test_check_expected_type_fails_on_wrong_data(): void
    {
        // Arrange
        $testedType = Faker::word();
        $type = Faker::word() . Faker::intId();
        $resource = $this->createResource($type, Faker::stringNumberId());

        // Expect
        $this->expectException(ResourceException::class);
        $this->expectExceptionMessage("Expected type '{$testedType}', got '{$type}'.");

        // Act
        $resource->checkExpectedType($testedType);
    }

    public function test_check_expected_type_do_nothing_on_valid_data(): void
    {
        // Arrange
        $type = Faker::word();
        $resource = $this->createResource($type, Faker::stringNumberId());

        // Expect
        $this->expectNotToPerformAssertions();

        // Act
        $resource->checkExpectedType($type);
    }

    /** @return iterable<string,array{\Closure}> */
    public static function wrongExpectedTypeAndIdProvider(): iterable
    {
        yield 'wrong type' => [
            static function (): array {
                $type = Faker::words(1);
                $wrongType = Faker::words(1);
                $id = Faker::intId();
                $stringId = (string) $id;

                return [
                    'type' => $wrongType,
                    'id' => $stringId,
                    'testedType' => $type,
                    'testedId' => $stringId,
                    'expectedExceptionMessage' => "Expected type '{$type}', got '{$wrongType}'.",
                ];
            },
        ];

        yield 'wrong id' => [
            static function (): array {
                $type = Faker::words(1);
                $id = Faker::intId();
                $stringId = (string) $id;
                $wrongId = Faker::intId();
                $stringWrongId = (string) $wrongId;

                return [
                    'type' => $type,
                    'id' => $stringWrongId,
                    'testedType' => $type,
                    'testedId' => $stringId,
                    'expectedExceptionMessage' => "Expected id '{$stringId}', got '{$wrongId}'.",
                ];
            },
        ];
    }

    /**
     * @param array<string,bool|int|string> $attributes
     * @param array<string,null|array{
     *     data?: array{
     *         id?: string,
     *         type?: string
     *         }
     *     }
     * > $relationships
     */
    private function createResource(
        string $type = '',
        string $id = '',
        array $attributes = [],
        array $relationships = [],
    ): JsonApiResource {
        return JsonApiResource::fromArray(
            [
                JsonApiKeywords::TYPE => $type,
                JsonApiKeywords::ID => $id,
                JsonApiKeywords::ATTRIBUTES => $attributes,
                JsonApiKeywords::RELATIONSHIPS => $relationships,
            ],
        );
    }
}
