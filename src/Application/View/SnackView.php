<?php

declare(strict_types=1);

namespace Tab\Application\View;

/**
 * @phpstan-type SnackData array{
 *      id?: int,
 *      name?: string,
 *  }
 */
final readonly class SnackView
{
    public const FIELD_RAW_ID = 'id';
    public const FIELD_RAW_NAME = 'name';

    private function __construct(
        public int $id,
        public string $name,
    ) {}

    /** @param SnackData $data */
    public static function fromArray(
        array $data,
    ): self {
        return new self(
            $data[self::FIELD_RAW_ID] ?? 0,
            $data[self::FIELD_RAW_NAME] ?? '',
        );
    }
}
