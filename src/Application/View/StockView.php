<?php

declare(strict_types=1);

namespace Polsl\Application\View;

final readonly class StockView
{
    private function __construct(
        public int $machineId,
        public int $snackId,
        public int $quantity,
        public string $snackName,
    ) {}

    /**
     * @param array{
     *     machine_id?: int|string|null,
     *     snack_id?: int|string|null,
     *     quantity?: int|string|null,
     *     snack_name?: string|null,
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) ($data['machine_id'] ?? 0),
            (int) ($data['snack_id'] ?? 0),
            (int) ($data['quantity'] ?? 0),
            $data['snack_name'] ?? '',
        );
    }
}
