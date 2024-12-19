<?php

declare(strict_types=1);

namespace Tab\Application\View;

/**
 * @phpstan-import-type SnackData from SnackView
 *
 * @phpstan-type MachineSnackData array{
 *      id?: int,
 *      position?: string,
 *      quantity?: int,
 *      snack?: SnackData,
 *  }
 */
final readonly class MachineSnackView
{
    public const FIELD_RAW_ID = 'id';
    public const FIELD_RAW_QUANTITY = 'quantity';
    public const FIELD_RAW_POSITION = 'position';
    public const FIELD_RAW_SNACK = 'snack';

    private function __construct(
        public int $id,
        public int $quantity,
        public string $position,
        public SnackView $snack,
    ) {
    }

    /** @param MachineSnackData $data */
    public static function fromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_RAW_ID] ?? 0,
            $data[self::FIELD_RAW_QUANTITY] ?? 0,
            $data[self::FIELD_RAW_POSITION] ?? '',
            SnackView::fromArray($data[self::FIELD_RAW_SNACK] ?? []),
        );
    }
}
