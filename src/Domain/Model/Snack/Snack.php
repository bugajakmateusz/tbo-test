<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Snack;

class Snack
{
    private int $id;

    private function __construct(
        private string $name,
    ) {
    }

    public static function create(
        Name $name,
    ): self {
        return new self(
            $name->toString(),
        );
    }
}
