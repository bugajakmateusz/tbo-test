<?php

declare(strict_types=1);

namespace Tab\Application\Schema;

use Tab\Application\View\SnackView;

final class SnackSchema extends AbstractSchema
{
    public const TYPE = 'snacks';
    public const ATTRIBUTE_NAME = 'name';

    public function resourceType(): string
    {
        return self::TYPE;
    }

    /** @param SnackView $resource */
    public function attributes(
        object $resource,
    ): array {
        return [
            self::ATTRIBUTE_NAME => $resource->name,
        ];
    }
}
