<?php

declare(strict_types=1);

namespace Tab\Packages\JsonApi\Application;

use Tab\Packages\JsonApi\Application\Exception\ResourceException;

final readonly class Relationships
{
    /** @param array<string, null|ResourceIdentifier> $relationships */
    private function __construct(private array $relationships) {}

    /**
     * @param array<
     *     string,
     *     null|array{data?: array{id?: string, type?: string}}|ResourceIdentifier
     * > $relationships
     */
    public static function fromArray(array $relationships): self
    {
        $mappedRelationships = [];
        foreach ($relationships as $name => $data) {
            if ($data instanceof ResourceIdentifier) {
                $mappedRelationships[$name] = $data;

                continue;
            }

            $rawData = $data[JsonApiKeywords::DATA] ?? null;
            $mappedRelationships[$name] = null === $rawData
                ? null
                : ResourceIdentifier::fromArray($rawData)
            ;
        }

        return new self($mappedRelationships);
    }

    public function hasRelationship(string $name): bool
    {
        return \array_key_exists($name, $this->relationships);
    }

    public function relationship(string $name): ?ResourceIdentifier
    {
        if (!$this->hasRelationship($name)) {
            throw new ResourceException(
                "There is no '{$name}' relationship in provided relationships.",
            );
        }

        return $this->relationships[$name];
    }

    /**
     * @return array<
     *     string,
     *     null|array{
     *         data: array{
     *             type: string,
     *             id: string,
     *         }
     *     }
     * >
     */
    public function toArray(): array
    {
        $relationships = [];

        /** @var null|ResourceIdentifier $resourceIdentifier */
        foreach ($this->relationships as $name => $resourceIdentifier) {
            $data = null === $resourceIdentifier
                ? null
                : [
                    JsonApiKeywords::DATA => [
                        JsonApiKeywords::TYPE => $resourceIdentifier->type(),
                        JsonApiKeywords::ID => $resourceIdentifier->id(),
                    ],
                ]
            ;
            $relationships[$name] = $data;
        }

        return $relationships;
    }
}
