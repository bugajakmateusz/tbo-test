<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\Collection;

use Tab\Packages\Collection\CollectionException;
use Tab\Packages\Collection\ObjectCollection;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\UnitTestCase;

/** @internal */
final class ObjectCollectionTest extends UnitTestCase
{
    public function test_collection_can_be_counted(): void
    {
        // Act
        $collection = ObjectCollection::fromObjects(new \stdClass());

        // Assert
        $directCount = $collection->count();
        self::assertCount(1, $collection);
        self::assertSame(1, $directCount);
    }

    public function test_collection_can_be_filtered(): void
    {
        // Arrange
        $object1 = new \stdClass();
        $object2 = new \stdClass();
        $object2->name = Faker::words(1);
        $filterFunction = static fn (object $object): bool => \property_exists($object, 'name');
        $collection = ObjectCollection::fromObjects($object1, $object2);

        // Act
        $filteredCollection = $collection->filterBy($filterFunction);

        // Assert
        self::assertCount(1, $filteredCollection);
        self::assertSame($object2, $filteredCollection->first());
        self::assertNotSame($collection, $filteredCollection);
    }

    public function test_first_returns_first_item(): void
    {
        // Arrange
        $object = new \stdClass();
        $collection = ObjectCollection::fromObjects($object, new \stdClass());

        // Act
        $first = $collection->first();

        // Assert
        self::assertSame($object, $first);
    }

    public function test_first_throws_when_collection_is_empty(): void
    {
        // Arrange
        $collection = ObjectCollection::fromObjects();

        // Assert
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage('Collection is empty.');

        // Act
        $collection->first();
    }

    public function test_last_returns_last_item(): void
    {
        // Arrange
        $object = new \stdClass();
        $collection = ObjectCollection::fromObjects(new \stdClass(), $object);

        // Act
        $last = $collection->last();

        // Assert
        self::assertSame($object, $last);
    }

    public function test_last_throws_when_collection_is_empty(): void
    {
        // Arrange
        $collection = ObjectCollection::fromObjects();

        // Assert
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage('Collection is empty.');

        // Act
        $collection->last();
    }

    public function test_collection_can_be_sorted(): void
    {
        // Arrange
        $array1 = new \SplFixedArray(1);
        $array2 = new \SplFixedArray(2);
        /** @var ObjectCollection<\SplFixedArray<mixed>> $collection */
        $collection = ObjectCollection::fromObjects($array1, $array2);

        // Act
        $sortedCollection = $collection->sort(
            static function (\SplFixedArray $left, \SplFixedArray $right): int {
                return $right->count() <=> $left->count();
            },
        );

        // Assert
        self::assertSame([$array2, $array1], $sortedCollection->toArray());
        self::assertNotSame($collection, $sortedCollection);
    }

    /**
     * @dataProvider namedConstructorsProvider
     *
     * @param mixed $argument
     */
    public function test_collection_can_be_created(string $constructorName, $argument): void
    {
        // Act
        $collection = ObjectCollection::{$constructorName}($argument);

        // Assert
        self::assertCount(1, $collection);
    }

    public function test_has(): void
    {
        // Arrange
        $object1 = new \stdClass();
        $object2 = new \stdClass();
        $collection = ObjectCollection::fromObjects($object1);

        // Act
        $hasObject1 = $collection->has($object1);
        $hasObject2 = $collection->has($object2);

        // Assert
        self::assertTrue($hasObject1);
        self::assertFalse($hasObject2);
    }

    /** @return iterable<string,array{string, object}> */
    public static function namedConstructorsProvider(): iterable
    {
        $object = new \stdClass();

        yield 'from objects' => ['fromObjects', $object];
    }
}
