<?php

declare(strict_types=1);

namespace Polsl\Packages\JsonApi\Infrastructure;

use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Polsl\Packages\JsonApi\Application\Exception\JsonApiException;
use Polsl\Packages\JsonApi\Application\Includes;
use Polsl\Packages\JsonApi\Application\JsonApiKeywords;
use Polsl\Packages\JsonApi\Application\Resource as JsonApiResource;
use Polsl\Packages\JsonApi\Application\ResourceIdentifierCollection;
use Polsl\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Polsl\Packages\JsonSerializer\JsonSerializerInterface;

final readonly class NeomerxJsonApiSerializer implements JsonApiSerializerInterface
{
    public function __construct(
        private EncoderInterface $encoder,
        private JsonSerializerInterface $jsonSerializer,
    ) {}

    public function encodeData(
        array|object $data,
        ?array $meta = null,
        ?Includes $includes = null,
        array $fieldSets = [],
    ): string {
        $this->encoder
            ->withIncludedPaths($includes?->toArray() ?? [])
            ->withFieldSets($fieldSets)
        ;

        if (null !== $meta) {
            $this->encoder
                ->withMeta($meta)
            ;
        }

        return $this->encoder
            ->encodeData($data)
        ;
    }

    public function decodeIdentifiers(string $jsonApiString): ResourceIdentifierCollection
    {
        /** @var mixed[] $identifiersRawData */
        $identifiersRawData = $this->jsonSerializer
            ->decode($jsonApiString, true)
        ;

        if (!\array_key_exists(JsonApiKeywords::DATA, $identifiersRawData)) {
            $key = JsonApiKeywords::DATA;

            throw new JsonApiException("Missing '{$key}' key in passed content.");
        }

        return ResourceIdentifierCollection::fromArray($identifiersRawData[JsonApiKeywords::DATA] ?? []);
    }

    public function encodeIdentifiers(array|object $data): string
    {
        return $this->encoder
            ->encodeIdentifiers($data)
        ;
    }

    public function decodeResource(string $jsonApiString): JsonApiResource
    {
        /** @var mixed[] $rawData */
        $rawData = $this->jsonSerializer
            ->decode($jsonApiString, true)
        ;

        if (!\array_key_exists(JsonApiKeywords::DATA, $rawData)) {
            $key = JsonApiKeywords::DATA;

            throw new JsonApiException("Missing '{$key}' key in passed content.");
        }

        return JsonApiResource::fromArray($rawData[JsonApiKeywords::DATA] ?? []);
    }
}
