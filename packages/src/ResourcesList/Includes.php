<?php

declare(strict_types=1);

namespace Tab\Packages\ResourcesList;

final class Includes
{
    /** @var string[] */
    private readonly array $includes;

    private function __construct(string ...$includes)
    {
        $this->includes = $includes;
    }

    public static function fromJsonApiString(string $includesString): self
    {
        $includes = \explode(',', $includesString);

        return new self(
            ...\array_filter(
                $includes,
                static fn (string $include): bool => '' !== $include,
            ),
        );
    }

    /** @return string[] */
    public function toArray(): array
    {
        return $this->includes;
    }
}
