<?php

declare(strict_types=1);

namespace Tab\Application\Schema;

use Tab\Application\View\UserView;

final class UserSchema extends AbstractSchema
{
    public const TYPE = 'users';
    public const ATTRIBUTE_EMAIL = 'email';
    public const ATTRIBUTE_NAME = 'name';
    public const ATTRIBUTE_SURNAME = 'surname';
    public const ATTRIBUTE_ROLES = 'roles';

    public function resourceType(): string
    {
        return self::TYPE;
    }

    /** @param UserView $resource */
    public function attributes(object $resource): array
    {
        return [
            self::ATTRIBUTE_EMAIL => $resource->email,
            self::ATTRIBUTE_NAME => $resource->name,
            self::ATTRIBUTE_SURNAME => $resource->surname,
            self::ATTRIBUTE_ROLES => $resource->roles,
        ];
    }
}
