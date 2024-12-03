<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Fields;

use Tab\Application\Schema\MachineSchema;
use Tab\Application\View\MachineView;
use Tab\Packages\Constants\Database\Tables;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\SqlExpressions\JsonObject;

final class MachineFieldsFactory
{
    private const DIRECT_ATTRIBUTES = [
        MachineSchema::ATTRIBUTE_LOCATION => [
            MachineView::FIELD_RAW_LOCATION,
            Tables\Machines::FIELD_LOCATION,
        ],
        MachineSchema::ATTRIBUTE_POSITIONS_NUMBER => [
            MachineView::FIELD_RAW_POSITIONS_NUMBER,
            Tables\Machines::FIELD_POSITIONS_NUMBER,
        ],
        MachineSchema::ATTRIBUTE_POSITIONS_CAPACITY => [
            MachineView::FIELD_RAW_POSITIONS_CAPACITY,
            Tables\Machines::FIELD_POSITIONS_CAPACITY,
        ],
    ];

    public function create(string $machinesTableAlias, Fields $typeFields): JsonObject
    {
        $machineFields = new JsonObject();
        $machineFields->addField(MachineView::FIELD_RAW_ID, "{$machinesTableAlias}.machine_id");
        if (false === $typeFields->hasType(MachineSchema::TYPE)) {
            return $machineFields;
        }

        $machineTypeFields = $typeFields->typeFields(MachineSchema::TYPE);
        $this->addDirectAttributes(
            $machineFields,
            $machineTypeFields,
            $machinesTableAlias,
        );

        return $machineFields;
    }

    private function addDirectAttributes(
        JsonObject $machineFields,
        Fields\TypeFields $machineTypeFields,
        string $tableAlias,
    ): void {
        foreach (self::DIRECT_ATTRIBUTES as $fieldName => $columns) {
            [
                $jsonColumn,
                $databaseColumn,
            ] = $columns;

            if (false === $machineTypeFields->hasField($fieldName)) {
                continue;
            }

            $machineFields->addField($jsonColumn, "{$tableAlias}.{$databaseColumn}");
        }
    }
}
