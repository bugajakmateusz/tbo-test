<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Client\JsonApi;

final class JsonApiDocument
{
    public function __construct(private readonly \stdClass $document) {}

    public function isList(): bool
    {
        $data = $this->document
            ->data ?? null;

        return \is_array($data);
    }

    /** @return string[] */
    public function ids(): array
    {
        if (!$this->isList()) {
            throw new \RuntimeException("Unable to collect 'ids' from non-list document.");
        }

        $data = $this->document
            ->data ?? [];

        $ids = [];
        foreach ($data as $resource) {
            $ids[] = (string) ($resource->id ?? '');
        }

        return $ids;
    }

    public function id(): string
    {
        if ($this->isList()) {
            throw new \RuntimeException("Unable to collect 'id' from list document.");
        }

        $id = $this->document
            ->data
            ->id
            ?? $this->document
            ->id
            ?? ''
        ;

        return $id;
    }

    public function resourcesCount(): int
    {
        if (!$this->isList()) {
            return 1;
        }

        $data = $this->document
            ->data ?? [];

        return \count($data);
    }

    public function resourceAt(int $index): self
    {
        if (!$this->isList()) {
            throw new \RuntimeException('Unable to fetch resource from resource.');
        }

        $resources = $this->document
            ->data ?? []
        ;

        if (!\array_key_exists($index, $resources)) {
            throw new \RuntimeException("Resource of index '{$index}' does not exists.");
        }

        return new self($resources[$index]);
    }

    public function type(): string
    {
        if ($this->isList()) {
            $types = \array_map(
                static fn (\stdClass $resource): string => $resource->type ?? '',
                $this->document
                    ->data ?? [],
            );

            $uniqueTypes = \array_unique($types);
            $typesCount = \count($uniqueTypes);

            if (1 !== $typesCount) {
                throw new \RuntimeException("Expected exactly one type, '{$typesCount}' found.");
            }

            $type = \reset($uniqueTypes);
        } else {
            $type = $this->document
                ->data
                ->type
                ?? $this->document
                ->type
                ?? ''
            ;
        }

        if (empty($type)) {
            throw new \RuntimeException('Expected non-empty type.');
        }

        return $type;
    }

    /** @return array<mixed> */
    public function meta(): array
    {
        $document = $this->document;

        return (array) ($document->meta ?? []);
    }

    /** @return array<string,mixed> */
    public function attributes(): array
    {
        if ($this->isList()) {
            throw new \RuntimeException('Unable to fetch attributes from list');
        }

        return (array) (
            $this->document
                ->data
                ->attributes
            ?? $this->document
                ->attributes
            ?? []
        );
    }

    /** @return array<int,null|mixed[]> */
    public function relationshipsArray(): array
    {
        $relationships = $this->extractRelationships();
        /**
         * @var list<
         *     array{
         *         data?: array<mixed>
         *     }
         * > $convertedArray
         */
        $convertedArray = $this->convertObjectToArray($relationships);

        return \array_map(
            static fn (array $relationship): ?array => $relationship['data'] ?? null,
            $convertedArray,
        );
    }

    public function relationship(string $name): self
    {
        $relationships = $this->extractRelationships();
        if (!\array_key_exists($name, $relationships)) {
            throw new \RuntimeException("Relationship '{$name}' not found.");
        }

        return new self($relationships[$name]);
    }

    public function hasInclude(string $id, string $type): bool
    {
        $matches = $this->findIncludes($id, $type);

        return \count($matches) > 0;
    }

    public function getInclude(string $id, string $type): self
    {
        $matches = $this->findIncludes($id, $type);
        if (0 === \count($matches)) {
            throw new \RuntimeException("Include '{$type}|{$id}' not found.");
        }

        return new self(\reset($matches));
    }

    /** @return \stdClass[] */
    private function findIncludes(string $id, string $type): array
    {
        $includes = (array) (
            $this->document
                ->data
                ->included
            ?? $this->document
                ->included
            ?? []
        );

        return \array_filter(
            $includes,
            static function (\stdClass $include) use ($id, $type): bool {
                $includeId = (string) ($include->id ?? '');
                $includeType = (string) ($include->type ?? '');

                return $id === $includeId && $includeType === $type;
            },
        );
    }

    /** @return array<string,\stdClass> */
    private function extractRelationships(): array
    {
        if ($this->isList()) {
            throw new \RuntimeException('Unable to fetch relationship from list.');
        }

        return (array) (
            $this->document
                ->data
                ->relationships
            ?? $this->document
                ->relationships
            ?? []
        );
    }

    private function convertObjectToArray(mixed $object): mixed
    {
        if (!\is_object($object) && !\is_array($object)) {
            return $object;
        }

        return \array_map(
            $this->convertObjectToArray(...),
            (array) $object,
        );
    }
}
