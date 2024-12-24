<?php

declare(strict_types=1);

namespace Tab\Application\View;

/**
 * @phpstan-import-type SnackData from SnackView
 *
 * @phpstan-type BuyData array{
 *      id?: int,
 *      price?: float,
 *      snack?: SnackData,
 *  }
 */
final readonly class BuyView
{
    public const FIELD_RAW_ID = 'id';
    public const FIELD_RAW_PRICE = 'price';
    public const FIELD_RAW_SNACK = 'snack';

    private function __construct(
        public int $id,
        public float $price,
        public SnackView $snack,
    ) {}

    /**
     * @param BuyData $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_RAW_ID] ?? 0,
            $data[self::FIELD_RAW_PRICE] ?? 0.0,
            SnackView::fromArray($data[self::FIELD_RAW_SNACK] ?? []),
        );
    }
}
