<?php

declare(strict_types=1);

namespace Polsl\Packages\Constants\Database\Tables;

use Polsl\Packages\Constants\Constants;

final class Users extends Constants
{
    public const FIELD_USER_ID = 'user_id';
    public const FIELD_EMAIL = 'email';
    public const FIELD_NAME = 'name';
    public const FIELD_SURNAME = 'surname';
    public const FIELD_PASSWORD_HASH = 'pass_hash';
    public const FIELD_ROLES = 'roles';
}
