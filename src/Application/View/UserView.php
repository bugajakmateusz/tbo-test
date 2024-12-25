<?php

declare(strict_types=1);

namespace Polsl\Application\View;

final readonly class UserView
{
    public const FIELD_RAW_ID = 'id';
    public const FIELD_RAW_EMAIL = 'email';
    public const FIELD_RAW_NAME = 'name';
    public const FIELD_RAW_SURNAME = 'surname';
    public const FIELD_RAW_ROLES = 'roles';

    /** @param string[] $roles */
    private function __construct(
        public int $id,
        public string $email,
        public string $name,
        public string $surname,
        public array $roles,
    ) {}

    /**
     * @param array{
     *     id?: int,
     *     email?: string,
     *     name?: string,
     *     surname?: string,
     *     roles?: string[],
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_RAW_ID] ?? 0,
            $data[self::FIELD_RAW_EMAIL] ?? '',
            $data[self::FIELD_RAW_NAME] ?? '',
            $data[self::FIELD_RAW_SURNAME] ?? '',
            $data[self::FIELD_RAW_ROLES] ?? [],
        );
    }
}
