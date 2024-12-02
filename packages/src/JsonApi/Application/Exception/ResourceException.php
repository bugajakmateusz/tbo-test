<?php

declare(strict_types=1);

namespace Tab\Packages\JsonApi\Application\Exception;

use Tab\Packages\JsonApi\Application\JsonApiKeywords;

final class ResourceException extends JsonApiException
{
    public static function emptyId(): self
    {
        return self::emptyKeyword(JsonApiKeywords::ID);
    }

    public static function emptyType(): self
    {
        return self::emptyKeyword(JsonApiKeywords::TYPE);
    }

    public static function notMatchingType(string $expectedType, string $actualType): self
    {
        return new self("Expected type '{$expectedType}', got '{$actualType}'.");
    }

    public static function notMatchingId(string $expectedId, string $actualId): self
    {
        return new self("Expected id '{$expectedId}', got '{$actualId}'.");
    }

    private static function emptyKeyword(string $keyword): self
    {
        return new self("Non-empty '{$keyword}' is required.");
    }
}
