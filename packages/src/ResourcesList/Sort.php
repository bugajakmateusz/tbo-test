<?php

declare(strict_types=1);

namespace Tab\Packages\ResourcesList;

final class Sort
{
    public const SORT_DIRECTION_ASC = 'ASC';
    public const SORT_DIRECTION_DESC = 'DESC';
    public const SUPPORTED_DIRECTIONS = [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC];

    private function __construct(
        private readonly string $name,
        private readonly string $direction,
    ) {}

    public static function fromJsonApiString(string $sort): self
    {
        $direction = self::SORT_DIRECTION_ASC;
        $name = $sort;

        if (($sort[0] ?? '') === '-') {
            $direction = self::SORT_DIRECTION_DESC;
            $name = \mb_substr($sort, 1);
        }

        if ('' === $name) {
            throw new \RuntimeException('Sort cannot be empty.');
        }

        self::checkDirection($direction);

        return new self($name, $direction);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function direction(): string
    {
        return $this->direction;
    }

    private static function checkDirection(string $direction): void
    {
        $supportedDirection = \in_array(
            $direction,
            self::SUPPORTED_DIRECTIONS,
            true,
        );

        if (!$supportedDirection) {
            throw new \RuntimeException("Direction '{$direction}' is not supported.");
        }
    }
}
