<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\JsonApi\Application;

use Tab\Packages\Faker\Faker;
use Tab\Packages\JsonApi\Application\Exception\ResourceIdentifierException;
use Tab\Packages\JsonApi\Application\JsonApiKeywords;
use Tab\Packages\JsonApi\Application\ResourceIdentifierCollection;
use Tab\Packages\TestCase\UnitTestCase;

/** @internal */
final class ResourceIdentifierCollectionTest extends UnitTestCase
{
    public function test_resource_identifier_collection_can_be_created_from_array(): void
    {
        $type = 'cities';
        $resourceIdsData = [
            $this->createResourceIdentifierData($type, Faker::stringNumberId()),
            $this->createResourceIdentifierData($type, Faker::stringNumberId()),
        ];
        $resourceIds = ResourceIdentifierCollection::fromArray($resourceIdsData);

        self::assertCount(2, $resourceIds->toArray());
    }

    public function test_check_type_will_throw_exception_on_invalid_type(): void
    {
        $type = 'teachers';
        $wrongId = Faker::stringNumberId();
        $wrongType = 'users';
        $resourceIdsData = [
            $this->createResourceIdentifierData($type, Faker::stringNumberId()),
            $this->createResourceIdentifierData($wrongType, $wrongId),
        ];
        $resourceIds = ResourceIdentifierCollection::fromArray($resourceIdsData);

        $this->expectException(ResourceIdentifierException::class);
        $this->expectExceptionMessage(
            "All resource identifiers should be of type '{$type}', but wrong id(s) are provided: 'type: {$wrongType}, id: {$wrongId}'.",
        );

        $resourceIds->checkType($type);
    }

    public function test_to_string_ids_returns_string_ids(): void
    {
        $type = 'schools';
        $id1 = Faker::stringNumberId();
        $id2 = Faker::stringNumberId();
        $resourceIdsData = [
            $this->createResourceIdentifierData($type, $id1),
            $this->createResourceIdentifierData($type, $id2),
        ];
        $resourceIds = ResourceIdentifierCollection::fromArray($resourceIdsData);

        self::assertSame(
            [$id1, $id2],
            $resourceIds->toStringIds(),
        );
    }

    /** @return array<string,string> */
    private function createResourceIdentifierData(string $type = '', string $id = ''): array
    {
        return [JsonApiKeywords::TYPE => $type, JsonApiKeywords::ID => $id];
    }
}
