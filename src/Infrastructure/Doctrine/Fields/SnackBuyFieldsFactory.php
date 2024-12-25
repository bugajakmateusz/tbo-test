<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Doctrine\Fields;

use Polsl\Application\Schema\SnackBuySchema;
use Polsl\Application\View\BuyView;
use Polsl\Packages\Constants\Database\Tables;
use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\SqlExpressions\JsonObject;

final readonly class SnackBuyFieldsFactory
{
    private const DIRECT_ATTRIBUTES = [
        SnackBuySchema::ATTRIBUTE_PRICE => [
            BuyView::FIELD_RAW_PRICE,
            Tables\WarehousePricesHistory::FIELD_BUY_PRICE,
        ],
    ];

    public function __construct(
        private SnackFieldsFactory $snackFieldsFactory,
    ) {}

    public function create(string $buysTableAlias, Fields $typeFields): JsonObject
    {
        $buyFields = new JsonObject();
        $buyFields->addField(BuyView::FIELD_RAW_ID, "{$buysTableAlias}.buy_id");
        if (false === $typeFields->hasType(SnackBuySchema::TYPE)) {
            return $buyFields;
        }

        $buyTypeFields = $typeFields->typeFields(SnackBuySchema::TYPE);
        $this->addDirectAttributes(
            $buyFields,
            $buyTypeFields,
            $buysTableAlias,
        );
        $this->addSnack(
            $buyFields,
            $buyTypeFields,
            $typeFields,
            $buysTableAlias,
        );

        return $buyFields;
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

            if (false === $machineTypeFields->hasField((string) $fieldName)) {
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
        if (false === $typeFields->hasField(SnackBuySchema::RELATIONSHIP_SNACK)) {
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

        $dataFields->addField(BuyView::FIELD_RAW_SNACK, "({$snackSql})");
    }
}
