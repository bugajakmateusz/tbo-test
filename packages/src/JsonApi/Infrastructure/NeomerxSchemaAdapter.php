<?php

declare(strict_types=1);

namespace Tab\Packages\JsonApi\Infrastructure;

use Neomerx\JsonApi\Contracts\Factories\FactoryInterface;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\BaseSchema;
use Tab\Packages\JsonApi\Contracts\SchemaInterface;

final class NeomerxSchemaAdapter extends BaseSchema
{
    public function __construct(
        FactoryInterface $factory,
        private readonly SchemaInterface $schema,
    ) {
        parent::__construct($factory);
    }

    public function getId($resource): string
    {
        return $this->schema
            ->id($resource)
        ;
    }

    /**
     * @param object $resource
     *
     * @return iterable<string, null|bool|int|mixed[]|string>
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return $this->schema
            ->attributes($resource)
        ;
    }

    /**
     * @param object $resource
     *
     * @return array<string,array<mixed,mixed>|bool|int|string>
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return \array_map(
            static fn ($resourceObject): array => [self::RELATIONSHIP_DATA => $resourceObject],
            $this->schema
                ->relationships($resource),
        );
    }

    /** @return array<mixed,mixed> */
    public function getLinks($resource): iterable
    {
        return [];
    }

    /** @param object $resource */
    public function hasResourceMeta($resource): bool
    {
        return !empty($this->getResourceMeta($resource));
    }

    /** @param object $resource */
    public function getResourceMeta($resource): mixed
    {
        return $this->schema
            ->primaryMeta($resource)
        ;
    }

    public function getType(): string
    {
        return $this->schema
            ->resourceType()
        ;
    }

    public function isAddSelfLinkInRelationshipByDefault(string $relationshipName): bool
    {
        return false;
    }

    public function isAddRelatedLinkInRelationshipByDefault(string $relationshipName): bool
    {
        return false;
    }
}
