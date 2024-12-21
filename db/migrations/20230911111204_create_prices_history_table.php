<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Tab\Packages\Constants\Database\Tables;

final class CreatePricesHistoryTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table(
            Tables::PRICES_HISTORY,
            [
                'id' => Tables\PricesHistory::FIELD_PRICE_ID,
            ],
        );
        $table
            ->addColumn(
                Tables\PricesHistory::FIELD_SNACK_ID,
                'integer',
                [
                    'null' => false,
                ],
            )
            ->addColumn(
                Tables\PricesHistory::FIELD_MACHINE_ID,
                'integer',
                [
                    'null' => false,
                ],
            )
            ->addColumn(
                Tables\PricesHistory::FIELD_PRICE,
                'float',
                [
                    'null' => false,
                    'signed' => false,
                ],
            )
            ->addColumn(
                Tables\PricesHistory::FIELD_CREATED_AT,
                'datetime',
                [
                    'null' => false,
                ],
            )
            ->addForeignKey(
                Tables\PricesHistory::FIELD_MACHINE_ID,
                Tables::MACHINES,
                Tables\Machines::FIELD_MACHINE_ID,
                ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'],
            )
            ->addForeignKey(
                Tables\PricesHistory::FIELD_SNACK_ID,
                Tables::SNACKS,
                Tables\Snacks::FIELD_SNACK_ID,
                ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'],
            )
        ;
        $table->create();
    }
}
