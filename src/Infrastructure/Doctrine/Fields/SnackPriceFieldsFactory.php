<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Doctrine\Fields;

use Polsl\Application\Schema\SnackPriceSchema;
use Polsl\Application\View\PriceView;
use Polsl\Packages\Constants\Database\Tables;
use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\SqlExpressions\JsonObject;

final readonly class SnackPriceFieldsFactory
{
    private const DIRECT_ATTRIBUTES = [
        SnackPriceSchema::ATTRIBUTE_PRICE => [
            PriceView::FIELD_RAW_PRICE,
            Tables\PricesHistory::FIELD_PRICE,
        ],
    ];

    public function __construct(
        private SnackFieldsFactory $snackFieldsFactory,
    ) {}

    public function create(string $pricesTableAlias, Fields $typeFields): JsonObject
    {
        $machineFields = new JsonObject();
        $machineFields->addField(PriceView::FIELD_RAW_ID, "{$pricesTableAlias}.price_id");
        if (false === $typeFields->hasType(SnackPriceSchema::TYPE)) {
            return $machineFields;
        }

        $machineTypeFields = $typeFields->typeFields(SnackPriceSchema::TYPE);
        $this->addDirectAttributes(
            $machineFields,
            $machineTypeFields,
            $pricesTableAlias,
        );
        $this->addSnack(
            $machineFields,
            $machineTypeFields,
            $typeFields,
            $pricesTableAlias,
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

    private function addSnack(
        JsonObject $dataFields,
        Fields\TypeFields $typeFields,
        Fields $fields,
        string $tableAlias,
    ): void {
        if (false === $typeFields->hasField(SnackPriceSchema::RELATIONSHIP_SNACK)) {
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

        $dataFields->addField(PriceView::FIELD_RAW_SNACK, "({$snackSql})");
    }
}
