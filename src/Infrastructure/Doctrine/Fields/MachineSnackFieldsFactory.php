<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Fields;

use Tab\Application\Schema\MachineSnackSchema;
use Tab\Application\View\MachineSnackView;
use Tab\Application\View\SnackView;
use Tab\Packages\Constants\Database\Tables;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\SqlExpressions\JsonObject;

final readonly class MachineSnackFieldsFactory
{
    private const DIRECT_ATTRIBUTES = [
        MachineSnackSchema::ATTRIBUTE_QUANTITY => [
            MachineSnackView::FIELD_RAW_QUANTITY,
            Tables\MachineSnack::FIELD_QUANTITY,
        ],
        MachineSnackSchema::ATTRIBUTE_POSITION => [
            MachineSnackView::FIELD_RAW_POSITION,
            Tables\MachineSnack::FIELD_POSITION,
        ],
    ];

    public function __construct(
        private SnackFieldsFactory $snackFieldsFactory,
    ) {
    }

    public function create(
        string $machineSnacksTableAlias,
        Fields $typeFields,
    ): JsonObject {
        $machineSnackFields = new JsonObject();
        $machineSnackFields->addField(
            SnackView::FIELD_RAW_ID,
            "{$machineSnacksTableAlias}.id",
        );
        if (false === $typeFields->hasType(MachineSnackSchema::TYPE)) {
            return $machineSnackFields;
        }

        $machineSnackTypeFields = $typeFields->typeFields(
            MachineSnackSchema::TYPE,
        );
        $this->addDirectAttributes(
            $machineSnackFields,
            $machineSnackTypeFields,
            $machineSnacksTableAlias,
        );
        $this->addSnack(
            $machineSnackFields,
            $machineSnackTypeFields,
            $typeFields,
            $machineSnacksTableAlias,
        );

        return $machineSnackFields;
    }

    private function addDirectAttributes(
        JsonObject $machineSnackFields,
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

            $machineSnackFields->addField(
                $jsonColumn,
                "{$tableAlias}.{$databaseColumn}",
            );
        }
    }

    private function addSnack(
        JsonObject $dataFields,
        Fields\TypeFields $typeFields,
        Fields $fields,
        string $tableAlias,
    ): void {
        if (false === $typeFields->hasField(MachineSnackSchema::RELATIONSHIP_SNACK)) {
            return;
        }

        $snacksTableAlias = 'snacks';
        $baseSnacksFields = $this->snackFieldsFactory
            ->create($snacksTableAlias, $fields)
        ;

        $snackSql = <<<"SQL"
            SELECT {$baseSnacksFields->toString()}
            FROM snacks {$snacksTableAlias}
            WHERE {$snacksTableAlias}.snack_id = {$tableAlias}.snack_id
            LIMIT 1
            SQL;

        $dataFields->addField(MachineSnackView::FIELD_RAW_SNACK, "({$snackSql})");
    }
}
