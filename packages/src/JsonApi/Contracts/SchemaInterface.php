<?php

declare(strict_types=1);

namespace Tab\Packages\JsonApi\Contracts;

interface SchemaInterface
{
    public function resourceType(): string;

    public function id(object $resource): string;

    /** @return array<string, null|bool|float|int|mixed[]|string> */
    public function attributes(object $resource): array;

    /** @return array<string,mixed> */
    public function relationships(object $resource): array;

    /** @return null|array<int|string,mixed> */
    public function primaryMeta(object $resource): ?array;
}
