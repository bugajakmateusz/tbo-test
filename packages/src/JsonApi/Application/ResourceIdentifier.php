<?php

declare(strict_types=1);

namespace Tab\Packages\JsonApi\Application;

use Tab\Packages\JsonApi\Application\Exception\ResourceException;
use Tab\Packages\JsonApi\Application\Exception\ResourceIdentifierException;

final readonly class ResourceIdentifier
{
    private function __construct(
        private string $id,
        private string $type,
    ) {
    }

    public static function fromScalars(string $id, string $type): self
    {
        self::checkId($id);
        self::checkType($type);

        return new self($id, $type);
    }

    /** @param array{id?: string, type?: string} $data */
    public static function fromArray(array $data): self
    {
        $id = (string) ($data[JsonApiKeywords::ID] ?? '');
        $type = $data[JsonApiKeywords::TYPE] ?? '';

        self::checkId($id);
        self::checkType($type);

        return new self($id, $type);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function checkExpectedType(string $expectedType): void
    {
        if ($expectedType !== $this->type) {
            throw ResourceException::notMatchingType($expectedType, $this->type);
        }
    }

    private static function checkId(string $id): void
    {
        if ('' === $id) {
            throw ResourceIdentifierException::emptyId();
        }
    }

    private static function checkType(string $type): void
    {
        if ('' === $type) {
            throw ResourceIdentifierException::emptyType();
        }
    }
}
