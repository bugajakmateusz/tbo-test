<?php

declare(strict_types=1);

namespace Tab\Packages\SqlExpressions;

final class OrderBy
{
    public const DIRECTION_ASC = 'ASC';
    public const DIRECTION_DESC = 'DESC';
    private const VALID_DIRECTION = [
        self::DIRECTION_ASC,
        self::DIRECTION_DESC,
    ];

    /** @var array<string, string> */
    private array $orders = [];

    public function add(string $column, string $direction): self
    {
        $this->checkColumn($column);
        $this->checkDirection($direction);

        $this->orders[$column] = $direction;

        return $this;
    }

    public function toString(): string
    {
        if (empty($this->orders)) {
            throw new \RuntimeException('At least one order is required.');
        }

        $orders = [];
        foreach ($this->orders as $column => $direction) {
            $orders[] = "{$column} {$direction}";
        }
        $ordersString = \implode(', ', $orders);

        return "ORDER BY {$ordersString}";
    }

    private function checkDirection(string $direction): void
    {
        if ('' === $direction) {
            throw new \RuntimeException('Direction cannot be empty.');
        }

        $isValidDirection = \in_array(
            $direction,
            self::VALID_DIRECTION,
            true,
        );

        if (!$isValidDirection) {
            $validDirections = \implode("', ", self::VALID_DIRECTION);

            throw new \RuntimeException(
                "Direction '{$direction}' is invalid, try one of: '{$validDirections}'.",
            );
        }
    }

    private function checkColumn(string $column): void
    {
        if ('' === $column) {
            throw new \RuntimeException('Column cannot be empty.');
        }
    }
}
