<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Fields;

use Tab\Application\Schema\SnackSchema;
use Tab\Application\View\SnackView;
use Tab\Packages\Constants\Database\Tables;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\SqlExpressions\JsonObject;

final class SnackFieldsFactory
{
    private const DIRECT_ATTRIBUTES = [
        SnackSchema::ATTRIBUTE_NAME => [
            SnackView::FIELD_RAW_NAME,
            Tables\Snacks::FIELD_NAME,
        ],
    ];

    public function create(
        string $snacksTableAlias,
        Fields $typeFields,
    ): JsonObject {
        $snackFields = new JsonObject();
        $snackFields->addField(
            SnackView::FIELD_RAW_ID,
            "{$snacksTableAlias}.snack_id",
        );
        if (false === $typeFields->hasType(SnackSchema::TYPE)) {
            return $snackFields;
        }

        $snackTypeFields = $typeFields->typeFields(
            SnackSchema::TYPE,
        );
        $this->addDirectAttributes(
            $snackFields,
            $snackTypeFields,
            $snacksTableAlias,
        );

        return $snackFields;
    }

    private function addDirectAttributes(
        JsonObject $snackFields,
        Fields\TypeFields $snackTypeFields,
        string $tableAlias,
    ): void {
        foreach (self::DIRECT_ATTRIBUTES as $fieldName => $columns) {
            [
                $jsonColumn,
                $databaseColumn,
            ] = $columns;

            if (false === $snackTypeFields->hasField($fieldName)) {
                continue;
            }

            $snackFields->addField(
                $jsonColumn,
                "{$tableAlias}.{$databaseColumn}",
            );
        }
    }
}
