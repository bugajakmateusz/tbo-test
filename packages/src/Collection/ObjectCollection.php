<?php

declare(strict_types=1);

namespace Polsl\Packages\Collection;

/**
 * @template Type of object
 *
 * @implements \IteratorAggregate<Type>
 */
final class ObjectCollection implements \Countable, \IteratorAggregate
{
    /** @var \SplObjectStorage<Type,null> */
    private readonly \SplObjectStorage $storage;

    /** @param Type $objects */
    private function __construct(object ...$objects)
    {
        /** @var \SplObjectStorage<Type,null> $storage */
        $storage = new \SplObjectStorage();
        foreach ($objects as $object) {
            $storage->attach($object);
        }

        $this->storage = $storage;
    }

    /**
     * @param Type $objects
     *
     * @return self<Type>
     */
    public static function fromObjects(object ...$objects): self
    {
        return new self(...$objects);
    }

    /**
     * @param callable(Type): bool $closure
     *
     * @return self<Type>
     */
    public function filterBy(callable $closure): self
    {
        return new self(
            ...\array_filter(
                [...$this->storage],
                $closure,
            ),
        );
    }

    /**
     * @param callable(Type, Type): int $closure
     *
     * @return self<Type>
     */
    public function sort(callable $closure): self
    {
        $items = $this->toArray();

        \usort($items, $closure);

        return new self(...$items);
    }

    /** @return Type[] */
    public function toArray(): array
    {
        return \iterator_to_array($this);
    }

    public function count(): int
    {
        return \count($this->storage);
    }

    /** @return Type */
    public function first(): object
    {
        $items = $this->toArray();
        $firstItem = \reset($items);
        if (false === $firstItem) {
            throw CollectionException::emptyCollection();
        }

        return $firstItem;
    }

    /** @return Type */
    public function last(): object
    {
        $items = $this->toArray();
        $lastItem = \end($items);
        if (false === $lastItem) {
            throw CollectionException::emptyCollection();
        }

        return $lastItem;
    }

    /** @return \Traversable<Type> */
    public function getIterator(): \Traversable
    {
        return $this->storage;
    }

    /** @param Type $object */
    public function has(object $object): bool
    {
        return $this->storage
            ->offsetExists($object)
        ;
    }
}
