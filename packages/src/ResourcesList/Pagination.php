<?php

declare(strict_types=1);

namespace Polsl\Packages\ResourcesList;

use Polsl\Packages\ResourcesList\Exception\ResourcesListException;

final class Pagination
{
    public const PAGE_RANGE = 5;

    private int $pageNumber;
    private readonly int $pageCount;
    /** @var int[] */
    private readonly array $pagesInRange;
    private readonly int $startPage;
    private readonly int $endPage;

    public function __construct(
        Page $page,
        TotalItems $totalItems,
        int $pageRange = self::PAGE_RANGE,
    ) {
        $totalItemsInt = $totalItems->toInt();
        $pageSize = $page->size();
        $this->pageNumber = $page->number();
        $this->pageCount = (int) \ceil($totalItemsInt / $pageSize);
        if ($this->pageCount < $this->pageNumber) {
            $this->pageNumber = $this->pageCount;
        }
        $pageRange = \min($pageRange, $this->pageCount);

        $this->pagesInRange = $this->calculatePagesInRange($pageRange);

        $proximity = \floor($pageRange / 2);
        $startPage = $this->pageNumber - $proximity;
        $endPage = $this->pageNumber + $proximity;
        if ($startPage < 1) {
            $endPage = \min($endPage + (1 - $startPage), $this->pageCount);
            $startPage = 1;
        }
        if ($endPage > $this->pageCount) {
            $startPage = \max($startPage - ($endPage - $this->pageCount), 1);
            $endPage = $this->pageCount;
        }

        $this->startPage = (int) $startPage;
        $this->endPage = (int) $endPage;
    }

    public function current(): int
    {
        return $this->pageNumber;
    }

    public function pageCount(): int
    {
        return $this->pageCount;
    }

    /** @return int[] */
    public function pagesInRange(): array
    {
        return $this->pagesInRange;
    }

    public function startPage(): int
    {
        return $this->startPage;
    }

    public function endPage(): int
    {
        return $this->endPage;
    }

    public function hasPreviousPage(): bool
    {
        return $this->pageNumber > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->pageNumber < $this->pageCount;
    }

    public function previous(): int
    {
        if (!$this->hasPreviousPage()) {
            throw new ResourcesListException('There is no previous page.');
        }

        return $this->pageNumber - 1;
    }

    public function next(): int
    {
        if (!$this->hasNextPage()) {
            throw new ResourcesListException('There is no next page.');
        }

        return $this->pageNumber + 1;
    }

    /** @return int[] */
    private function calculatePagesInRange(int $pageRange): array
    {
        $delta = \ceil($pageRange / 2);
        if ($this->pageNumber - $delta > $this->pageCount - $pageRange) {
            $pages = \range($this->pageCount - $pageRange + 1, $this->pageCount);
        } else {
            if ($this->pageNumber - $delta < 0) {
                $delta = $this->pageNumber;
            }
            $offset = $this->pageNumber - $delta;
            $pages = \range($offset + 1, $offset + $pageRange);
        }

        return \array_map('intval', $pages);
    }
}
