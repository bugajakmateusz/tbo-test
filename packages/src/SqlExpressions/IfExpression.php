<?php

declare(strict_types=1);

namespace Tab\Packages\SqlExpressions;

final readonly class IfExpression
{
    public function __construct(
        private string $condition,
        private string $then,
        private string $else,
    ) {
    }

    public function toString(): string
    {
        return "IF ({$this->condition}, {$this->then}, {$this->else})";
    }
}
