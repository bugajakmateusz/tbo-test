<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Tab\Packages\Constants\Database\Tables;

final class CreateWarehousePricesHistoryTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table(
            Tables::WAREHOUSE_PRICES_HISTORY,
            [
                'id' => Tables\WarehousePricesHistory::FIELD_BUY_ID,
            ],
        );
        $table
            ->addColumn(
                Tables\WarehousePricesHistory::FIELD_SNACK_ID,
                'integer',
                [
                    'null' => false,
                ],
            )
            ->addColumn(
                Tables\WarehousePricesHistory::FIELD_BUY_PRICE,
                'float',
                [
                    'null' => false,
                    'signed' => false,
                ],
            )
            ->addColumn(
                Tables\WarehousePricesHistory::FIELD_QUANTITY,
                'integer',
                [
                    'null' => false,
                ],
            )
            ->addColumn(
                Tables\WarehousePricesHistory::FIELD_BUY_DATE,
                'datetime',
                [
                    'null' => false,
                ],
            )
            ->addForeignKey(
                Tables\WarehousePricesHistory::FIELD_SNACK_ID,
                Tables::SNACKS,
                Tables\Snacks::FIELD_SNACK_ID,
                [
                    'delete' => 'NO_ACTION',
                    'update' => 'NO_ACTION',
                ],
            );
        $table->create();
    }
}
