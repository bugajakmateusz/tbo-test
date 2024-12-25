<?php

declare(strict_types=1);

namespace Polsl\Domain;

final class EntityNotFoundException extends DomainException
{
    public static function create(int|string $id, string $type): self
    {
        $type = self::resolveShortName($type);

        return new self("Entity of type '{$type}' (id: {$id}) was not found.");
    }

    public static function createForField(
        string $fieldName,
        int|string $fieldValue,
        string $type,
    ): self {
        $type = self::resolveShortName($type);

        return new self(
            "Entity of type '{$type}' with param '{$fieldName}' = '{$fieldValue}' was not found.",
        );
    }

    private static function resolveShortName(string $type): string
    {
        if (\class_exists($type)) {
            $type = (new \ReflectionClass($type))->getShortName();
        }

        return $type;
    }
}
