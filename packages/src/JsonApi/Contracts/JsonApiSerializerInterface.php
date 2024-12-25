<?php

declare(strict_types=1);

namespace Polsl\Packages\JsonApi\Contracts;

use Polsl\Packages\JsonApi\Application\Includes;
use Polsl\Packages\JsonApi\Application\Resource as JsonApiResource;
use Polsl\Packages\JsonApi\Application\ResourceIdentifierCollection;

interface JsonApiSerializerInterface
{
    /**
     * @param object|object[]             $data
     * @param array<string,array<string>> $fieldSets
     * @param null|array<mixed,mixed>     $meta
     */
    public function encodeData(
        array|object $data,
        ?array $meta = null,
        ?Includes $includes = null,
        array $fieldSets = [],
    ): string;

    /** @param object|object[] $data */
    public function encodeIdentifiers(array|object $data): string;

    public function decodeIdentifiers(string $jsonApiString): ResourceIdentifierCollection;

    public function decodeResource(string $jsonApiString): JsonApiResource;
}
