<?php

declare(strict_types=1);

namespace Polsl\Tests\TestCase\Application\PropertyAccess;

final class PropertyManipulator
{
    private static ?self $instance = null;

    /** @param array<string, mixed> $properties */
    public function propertiesSet(object $object, array $properties): void
    {
        $setterClosure = function () use ($object, $properties): void {
            foreach ($properties as $name => $value) {
                if (!\property_exists($object, $name)) {
                    $class = $object::class;

                    throw new \RuntimeException(
                        "Property '{$name}' does not exists in '{$class}' class.",
                    );
                }

                $this->{$name} = $value;
            }
        };

        $setterClosure->call($object);
    }

    public function propertySet(
        object $object,
        string $property,
        mixed $value,
    ): void {
        $this->propertiesSet($object, [$property => $value]);
    }

    /** @return array<string, mixed> */
    public function propertiesGetAll(object $object): array
    {
        $getterClosure = fn (): array => \get_object_vars($this);

        return $getterClosure->call($object);
    }

    /**
     * @param string[] $properties
     *
     * @return array<string, mixed>
     */
    public function propertiesGet(object $object, array $properties): array
    {
        $getterClosure = function () use ($object, $properties): array {
            $values = [];
            foreach ($properties as $name) {
                if (!\property_exists($object, $name)) {
                    $class = $object::class;

                    throw new \RuntimeException(
                        "Property '{$name}' does not exists in '{$class}' class.",
                    );
                }

                $values[$name] = $this->{$name};
            }

            return $values;
        };

        return $getterClosure->call($object);
    }

    public function propertyGet(object $object, string $property): mixed
    {
        $properties = $this->propertiesGet($object, [$property]);

        return \reset($properties);
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }
}
