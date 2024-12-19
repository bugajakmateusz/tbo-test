<?php

declare(strict_types=1);

namespace Tab\Application\View;

/**
 * @phpstan-type SnackData array{
 *      id?: int,
 *      name?: string,
 *      quantity?: int,
 *  }
 */
final readonly class SnackView
{
    public const FIELD_RAW_ID = 'id';
    public const FIELD_RAW_NAME = 'name';
    public const FIELD_RAW_QUANTITY = 'quantity';

    private function __construct(
        public int $id,
        public string $name,
        public int $quantity,
    ) {}

    /** @param SnackData $data */
    public static function fromArray(
        array $data,
    ): self {
        return new self(
            $data[self::FIELD_RAW_ID] ?? 0,
            $data[self::FIELD_RAW_NAME] ?? '',
            $data[self::FIELD_RAW_QUANTITY] ?? 0,
        );
    }
}
