<?php

declare(strict_types=1);

namespace Tab\Application\View;

final readonly class MachineView
{
    public const FIELD_RAW_ID = 'id';
    public const FIELD_RAW_LOCATION = 'location';
    public const FIELD_RAW_POSITIONS_NUMBER = 'positions_no';
    public const FIELD_RAW_POSITIONS_CAPACITY = 'positions_capacity';

    public function __construct(
        public int $id,
        public string $location,
        public int $positionsNo,
        public int $positionsCapacity,
    ) {
    }

    /**
     * @param array{
     *     id?: int,
     *     location?: string,
     *     positions_no?: int,
     *     positions_capacity?: int,
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_RAW_ID] ?? 0,
            $data[self::FIELD_RAW_LOCATION] ?? '',
            $data[self::FIELD_RAW_POSITIONS_NUMBER] ?? 0,
            $data[self::FIELD_RAW_POSITIONS_CAPACITY] ?? 0,
        );
    }
}
