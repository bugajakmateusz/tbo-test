<?php

declare(strict_types=1);

namespace Tab\Packages\JsonApi\Contracts;

use Tab\Packages\JsonApi\Application\Includes;
use Tab\Packages\JsonApi\Application\Resource as JsonApiResource;
use Tab\Packages\JsonApi\Application\ResourceIdentifierCollection;

interface JsonApiSerializerInterface
{
    /**
     * @param object|object[]             $data
     * @param array<string,array<string>> $fieldSets
     * @param null|array<mixed,mixed>     $meta
     */
    public function encodeData(
        object|array $data,
        array $meta = null,
        Includes $includes = null,
        array $fieldSets = [],
    ): string;

    /** @param object|object[] $data */
    public function encodeIdentifiers(object|array $data): string;

    public function decodeIdentifiers(string $jsonApiString): ResourceIdentifierCollection;

    public function decodeResource(string $jsonApiString): JsonApiResource;
}
