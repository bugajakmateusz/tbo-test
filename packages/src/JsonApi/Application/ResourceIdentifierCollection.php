<?php

declare(strict_types=1);

namespace Polsl\Packages\JsonApi\Application;

use Polsl\Packages\JsonApi\Application\Exception\ResourceIdentifierException;

final readonly class ResourceIdentifierCollection
{
    /** @var ResourceIdentifier[] */
    private array $resourceIdentifiers;

    private function __construct(ResourceIdentifier ...$resourceIdentifiers)
    {
        $this->resourceIdentifiers = $resourceIdentifiers;
    }

    /**
     * @param array<
     *     array{
     *         id?: string,
     *         type?: string,
     *     }
     * > $resourceIdentifiersData
     */
    public static function fromArray(array $resourceIdentifiersData): self
    {
        return new self(
            ...\array_map(
                static function (array $resourceIdentifierData): ResourceIdentifier {
                    return ResourceIdentifier::fromArray($resourceIdentifierData);
                },
                $resourceIdentifiersData,
            ),
        );
    }

    /** @return ResourceIdentifier[] */
    public function toArray(): array
    {
        return $this->resourceIdentifiers;
    }

    public function checkType(string $type): void
    {
        $wrongTypeIds = \array_filter(
            $this->resourceIdentifiers,
            static function (ResourceIdentifier $resourceIdentifier) use ($type): bool {
                return $resourceIdentifier->type() !== $type;
            },
        );

        if (\count($wrongTypeIds) > 0) {
            throw ResourceIdentifierException::unexpectedIdsProvided($type, $wrongTypeIds);
        }
    }

    /** @return string[] */
    public function toStringIds(): array
    {
        return \array_map(
            static fn (ResourceIdentifier $resourceIdentifier): string => $resourceIdentifier->id(),
            $this->resourceIdentifiers,
        );
    }
}
