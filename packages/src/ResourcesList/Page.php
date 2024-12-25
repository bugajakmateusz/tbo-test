<?php

declare(strict_types=1);

namespace Polsl\Packages\ResourcesList;

final class Page
{
    public const DEFAULT_NUMBER = 1;
    public const DEFAULT_SIZE = 25;
    public const ANY_NUMBER_THRESHOLD = 5;
    public const SIZES = [
        10,
        15,
        20,
        25,
        50,
        100,
    ];

    private function __construct(
        private readonly int $pageNumber,
        private readonly int $pageSize,
    ) {}

    /** @param array<string,int|string> $data */
    public static function fromArray(
        array $data,
        int $defaultNumber = self::DEFAULT_NUMBER,
        int $defaultSize = self::DEFAULT_SIZE,
    ): self {
        $pageNumber = (int) ($data['number'] ?? $defaultNumber);
        $pageSize = (int) ($data['size'] ?? $defaultSize);

        return self::fromScalars($pageNumber, $pageSize);
    }

    public static function fromScalars(int $number, int $size): self
    {
        self::checkPageNumber($number);
        self::checkPageSize($size);

        return new self($number, $size);
    }

    public function size(): int
    {
        return $this->pageSize;
    }

    public function number(): int
    {
        return $this->pageNumber;
    }

    public function offset(): int
    {
        return ($this->pageNumber - 1) * $this->pageSize;
    }

    private static function checkPageNumber(int $pageNumber): void
    {
        if ($pageNumber < 1) {
            throw new PageException("Page number cannot be lower than '1', '{$pageNumber}' passed.");
        }
    }

    private static function checkPageSize(int $pageSize): void
    {
        if ($pageSize < 1) {
            throw new PageException("Page size cannot be lower than '1', '{$pageSize}' passed.");
        }

        if ($pageSize <= self::ANY_NUMBER_THRESHOLD) {
            return;
        }

        $allowedSize = \in_array(
            $pageSize,
            self::SIZES,
            true,
        );

        if (!$allowedSize) {
            $anyNumberRange = '1-' . self::ANY_NUMBER_THRESHOLD;
            $sizes = self::SIZES;
            \array_unshift($sizes, $anyNumberRange);
            $allowedSizes = \implode("', '", $sizes);

            throw new PageException("Page size '{$pageSize}' is not allowed, try one of these: '{$allowedSizes}'.");
        }
    }
}
