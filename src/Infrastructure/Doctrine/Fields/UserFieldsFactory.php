<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Fields;

use Tab\Application\Schema\UserSchema;
use Tab\Application\View\UserView;
use Tab\Packages\Constants\Database\Tables;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\SqlExpressions\JsonObject;

final class UserFieldsFactory
{
    private const DIRECT_ATTRIBUTES = [
        UserSchema::ATTRIBUTE_EMAIL => [
            UserView::FIELD_RAW_EMAIL,
            Tables\Users::FIELD_EMAIL,
        ],
        UserSchema::ATTRIBUTE_NAME => [
            UserView::FIELD_RAW_NAME,
            Tables\Users::FIELD_NAME,
        ],
        UserSchema::ATTRIBUTE_SURNAME => [
            UserView::FIELD_RAW_SURNAME,
            Tables\Users::FIELD_SURNAME,
        ],
        UserSchema::ATTRIBUTE_ROLES => [
            UserView::FIELD_RAW_ROLES,
            Tables\Users::FIELD_ROLES,
        ],
    ];

    public function create(string $usersTableAlias, Fields $typeFields): JsonObject
    {
        $userFields = new JsonObject();
        $userFields->addField(UserView::FIELD_RAW_ID, "{$usersTableAlias}.user_id");
        if (false === $typeFields->hasType(UserSchema::TYPE)) {
            return $userFields;
        }

        $userTypeFields = $typeFields->typeFields(UserSchema::TYPE);
        $this->addDirectAttributes(
            $userFields,
            $userTypeFields,
            $usersTableAlias,
        );

        return $userFields;
    }

    private function addDirectAttributes(
        JsonObject $userFields,
        Fields\TypeFields $userTypeFields,
        string $tableAlias,
    ): void {
        foreach (self::DIRECT_ATTRIBUTES as $fieldName => $columns) {
            [
                $jsonColumn,
                $databaseColumn,
            ] = $columns;

            if (false === $userTypeFields->hasField($fieldName)) {
                continue;
            }

            $userFields->addField($jsonColumn, "{$tableAlias}.{$databaseColumn}");
        }
    }
}
