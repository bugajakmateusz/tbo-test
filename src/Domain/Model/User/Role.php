<?php

declare(strict_types=1);

namespace Tab\Domain\Model\User;

enum Role: string
{
    case USER = 'ROLE_USER';
    case ADMIN = 'ROLE_ADMIN';
    case WAREHOUSE_OPERATOR = 'ROLE_WAREHOUSE_OPERATOR';
    case OFFICE_MANAGER = 'ROLE_OFFICE_MANAGER';

    /** @return array <string> */
    public static function list(): array
    {
        return \array_column(self::cases(), 'value');
    }
}
