<?php

declare(strict_types=1);

namespace Tab\Packages\ResourcesList\Fields;

final class TypeFields
{
    /** @param string[] $fields */
    private function __construct(
        private readonly string $type,
        private readonly array $fields,
        private readonly bool $allFields = false,
    ) {
    }

    /** @param string[] $fields */
    public static function create(string $type, array $fields): self
    {
        self::checkType($type);

        return new self(
            $type,
            \array_map(
                static fn (string $field): string => $field,
                $fields,
            ),
        );
    }

    public static function createAllFields(string $type): self
    {
        self::checkType($type);

        return new self(
            $type,
            [],
            true,
        );
    }

    public function hasField(string $fieldName): bool
    {
        if ($this->allFields) {
            return true;
        }

        return \in_array(
            $fieldName,
            $this->fields,
            true,
        );
    }

    public function type(): string
    {
        return $this->type;
    }

    /** @return string[] */
    public function fields(): array
    {
        return $this->fields;
    }

    public function isAllFields(): bool
    {
        return $this->allFields;
    }

    private static function checkType(string $type): void
    {
        if ('' === $type) {
            throw new \RuntimeException('Empty type is not allowed.');
        }
    }
}
