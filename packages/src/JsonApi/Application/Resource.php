<?php

declare(strict_types=1);

namespace Polsl\Packages\JsonApi\Application;

use Polsl\Packages\JsonApi\Application\Exception\ResourceException;

final readonly class Resource
{
    /** @param array<string,mixed> $attributes */
    private function __construct(
        private string $id,
        private string $type,
        private array $attributes,
        private Relationships $relationships,
    ) {}

    /**
     * @param array{
     *     id?: string,
     *     type?: string,
     *     attributes?: array<string, mixed>,
     *     relationships?: array<
     *         string,
     *         null|array{data?: array{id?: string, type?: string}}|ResourceIdentifier
     *     >
     * } $data
     */
    public static function fromArray(array $data): self
    {
        $id = (string) ($data[JsonApiKeywords::ID] ?? '');
        $type = $data[JsonApiKeywords::TYPE] ?? '';
        $attributes = $data[JsonApiKeywords::ATTRIBUTES] ?? [];
        $relationships = $data[JsonApiKeywords::RELATIONSHIPS] ?? [];

        self::checkType($type);

        return new self(
            $id,
            $type,
            $attributes,
            Relationships::fromArray($relationships),
        );
    }

    public function id(): string
    {
        self::checkId($this->id);

        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    /** @return array<string,mixed> */
    public function attributes(): array
    {
        return $this->attributes;
    }

    public function relationships(): Relationships
    {
        return $this->relationships;
    }

    public function checkExpectedType(string $expectedType): void
    {
        if ($expectedType !== $this->type) {
            throw ResourceException::notMatchingType($expectedType, $this->type);
        }
    }

    public function checkExpectedTypeAndId(string $expectedId, string $expectedType): void
    {
        $this->checkExpectedType($expectedType);

        if ($this->id !== $expectedId) {
            throw ResourceException::notMatchingId($expectedId, $this->id);
        }
    }

    private static function checkId(string $id): void
    {
        if ('' === $id) {
            throw ResourceException::emptyId();
        }
    }

    private static function checkType(string $type): void
    {
        if ('' === $type) {
            throw ResourceException::emptyType();
        }
    }
}
