<?php

declare(strict_types=1);

namespace Polsl\Application\View;

final readonly class MachineReportView
{
    /** @param StockView[] $stockViews */
    private function __construct(
        public array $stockViews,
    ) {}

    /**
     * @param list<array{
     *     machine_id?: int|string|null,
     *     snack_id?: int|string|null,
     *     quantity?: int|string|null,
     *     snack_name?: string|null,
     * }> $data
     */
    public static function fromArray(array $data): self
    {
        \dump($data);

        return new self(
            \array_map(
                static fn (array $stockData): StockView => StockView::fromArray($stockData),
                $data,
            ),
        );
    }
}
