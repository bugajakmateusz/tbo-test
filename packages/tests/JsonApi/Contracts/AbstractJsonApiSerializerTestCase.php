<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\JsonApi\Contracts;

use Tab\Packages\Faker\Faker;
use Tab\Packages\JsonApi\Application\JsonApiKeywords;
use Tab\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Tab\Packages\TestCase\UnitTestCase;

abstract class AbstractJsonApiSerializerTestCase extends UnitTestCase
{
    abstract public function createSerializer(): JsonApiSerializerInterface;

    public function test_resource_identifiers_can_be_decoded(): void
    {
        $identifiers = [
            'data' => [
                [
                    'type' => 'projects',
                    'id' => Faker::intId(),
                ],
                [
                    'type' => 'projects',
                    'id' => Faker::intId(),
                ],
            ],
        ];

        $serializer = $this->createSerializer();
        $resourceIdentifiers = $serializer->decodeIdentifiers(
            $this->jsonEncode(
                $identifiers,
            ),
        );

        self::assertCount(2, $resourceIdentifiers->toArray());
    }

    public function test_resource_can_be_decoded(): void
    {
        $id = Faker::stringNumberId();
        $type = 'users';
        $attributes = ['active' => true];
        $resource = [
            JsonApiKeywords::DATA => [
                JsonApiKeywords::ID => $id,
                JsonApiKeywords::TYPE => $type,
                JsonApiKeywords::ATTRIBUTES => $attributes,
            ],
        ];

        $serializer = $this->createSerializer();
        $resource = $serializer->decodeResource(
            $this->jsonEncode(
                $resource,
            ),
        );

        self::assertSame($id, $resource->id());
        self::assertSame($type, $resource->type());
        self::assertSame($attributes, $resource->attributes());
    }

    public function test_encode_data_do_not_add_meta_on_null_meta(): void
    {
        // Arrange
        $serializer = $this->createSerializer();
        $resourceId = Faker::intId();
        $resource = new \stdClass();
        $resource->id = $resourceId;
        $expectedEncodedData = $this->jsonEncode(
            [
                'data' => [
                    'type' => 'std',
                    'id' => (string) $resourceId,
                ],
            ],
        );

        // Act
        $encodedData = $serializer->encodeData($resource);

        // Assert
        self::assertJsonStringEqualsJsonString($expectedEncodedData, $encodedData);
    }
}
