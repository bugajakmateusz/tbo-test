<?php

declare(strict_types=1);

namespace Polsl\Packages\ResourcesList;

final class TotalItems
{
    private function __construct(private readonly int $totalItems) {}

    public static function fromInt(int $totalItems): self
    {
        self::checkTotalItems($totalItems);

        return new self($totalItems);
    }

    public function toInt(): int
    {
        return $this->totalItems;
    }

    private static function checkTotalItems(int $totalItems): void
    {
        if ($totalItems < 0) {
            throw new \RuntimeException("Total items count cannot be lower than '0', '{$totalItems}' passed.");
        }
    }
}
