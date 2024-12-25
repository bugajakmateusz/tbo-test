<?php

declare(strict_types=1);

namespace Polsl\Packages\JsonApi\Application\Exception;

use Polsl\Packages\JsonApi\Application\JsonApiKeywords;
use Polsl\Packages\JsonApi\Application\ResourceIdentifier;

final class ResourceIdentifierException extends JsonApiException
{
    public static function emptyId(): self
    {
        return self::emptyKeyword(JsonApiKeywords::ID);
    }

    public static function emptyType(): self
    {
        return self::emptyKeyword(JsonApiKeywords::TYPE);
    }

    /** @param ResourceIdentifier[] $wrongIds */
    public static function unexpectedIdsProvided(string $expectedType, array $wrongIds): self
    {
        $wrongIdsString = \implode(
            "', '",
            \array_map(
                static function (ResourceIdentifier $resourceIdentifier): string {
                    return "type: {$resourceIdentifier->type()}, id: {$resourceIdentifier->id()}";
                },
                $wrongIds,
            ),
        );

        return new self(
            "All resource identifiers should be of type '{$expectedType}', but wrong id(s) are provided: '{$wrongIdsString}'.",
        );
    }

    private static function emptyKeyword(string $keyword): self
    {
        return new self("Non-empty '{$keyword}' is required.");
    }
}
