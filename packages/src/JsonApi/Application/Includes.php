<?php

declare(strict_types=1);

namespace Tab\Packages\JsonApi\Application;

final class Includes
{
    /** @var string[] */
    private readonly array $includes;

    private function __construct(string ...$includes)
    {
        $this->includes = $includes;
    }

    /** @param string[] $includes */
    public static function fromArray(array $includes): self
    {
        return new self(...\array_values($includes));
    }

    /** @return string[] */
    public function toArray(): array
    {
        return $this->includes;
    }
}
