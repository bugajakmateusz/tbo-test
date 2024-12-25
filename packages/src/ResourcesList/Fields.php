<?php

declare(strict_types=1);

namespace Polsl\Packages\ResourcesList;

use Polsl\Packages\ResourcesList\Fields\TypeFields;

final class Fields
{
    /** @var TypeFields[] */
    private readonly array $typeFields;

    private function __construct(TypeFields ...$typeFields)
    {
        $this->typeFields = $typeFields;
    }

    /** @param array<string,string> $data */
    public static function createFromStrings(array $data): self
    {
        $fieldSets = \array_map(
            static fn (string $fields): array => \array_map(
                \trim(...),
                \explode(',', $fields),
            ),
            $data,
        );

        return self::createFromArray($fieldSets);
    }

    /** @param array<string,array<string>>|TypeFields[] $data */
    public static function createFromArray(array $data): self
    {
        $fields = [];
        foreach ($data as $type => $typeFields) {
            if ($typeFields instanceof TypeFields) {
                $fields[] = $typeFields;

                continue;
            }

            $fields[] = TypeFields::create(
                $type,
                $typeFields,
            );
        }

        return new self(...$fields);
    }

    public static function createAllFieldsForType(string $type): self
    {
        return new self(TypeFields::createAllFields($type));
    }

    public static function createFieldsForType(string $type, string ...$fields): self
    {
        return new self(
            TypeFields::create(
                $type,
                $fields,
            ),
        );
    }

    public function typeFields(string $type): TypeFields
    {
        $typeFieldsArray = $this->filterByType($type);
        if (1 !== \count($typeFieldsArray)) {
            throw new \RuntimeException("Unable to find fields for type '{$type}'.");
        }

        return \reset($typeFieldsArray);
    }

    public function hasType(string $type): bool
    {
        $typeFieldsArray = $this->filterByType($type);

        return [] !== $typeFieldsArray;
    }

    /** @return array<string,array<string>> */
    public function toArray(bool $ignoreAllFields = true): array
    {
        $typeFields = $this->typeFields;
        if ($ignoreAllFields) {
            $typeFields = \array_filter(
                $typeFields,
                static fn (TypeFields $typeFields): bool => !$typeFields->isAllFields(),
            );
        }

        return \array_column(
            \array_map(
                static fn (TypeFields $typeFields): array => [
                    'value' => $typeFields->fields(),
                    'key' => $typeFields->type(),
                ],
                $typeFields,
            ),
            'value',
            'key',
        );
    }

    public function isEmpty(): bool
    {
        return [] === $this->typeFields;
    }

    /** @return TypeFields[] */
    private function filterByType(string $type): array
    {
        return \array_filter(
            $this->typeFields,
            static fn (TypeFields $typeFields): bool => $typeFields->type() === $type,
        );
    }
}
