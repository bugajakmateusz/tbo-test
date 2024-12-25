<?php

declare(strict_types=1);

namespace Polsl\Packages\Validator;

final readonly class Violations
{
    /** @var ViolationInterface[] */
    private array $violations;

    private function __construct(ViolationInterface ...$violations)
    {
        $this->violations = $violations;
    }

    public static function fromViolations(ViolationInterface ...$violations): self
    {
        return new self(...$violations);
    }

    public function isEmpty(): bool
    {
        return 0 === \count($this->violations);
    }

    /** @return ViolationInterface[] */
    public function toArray(): array
    {
        return $this->violations;
    }
}
