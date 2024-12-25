<?php

declare(strict_types=1);

namespace Polsl\Application\Schema;

use Polsl\Packages\JsonApi\Contracts\SchemaInterface;

abstract class AbstractSchema implements SchemaInterface
{
    public function id(object $resource): string
    {
        // @phpstan-ignore-next-line
        return (string) $resource->id;
    }

    public function attributes(object $resource): array
    {
        return [];
    }

    public function relationships(object $resource): array
    {
        return [];
    }

    public function primaryMeta(object $resource): ?array
    {
        return [];
    }
}
