<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Tab\Packages\Constants\Database\Tables;

final class AddSnackSellsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table(
            Tables::SNACK_SELLS,
            [
                'id' => Tables\SnackSells::FIELD_SELL_ID,
            ],
        );

        $table
            ->addColumn(
                Tables\SnackSells::FIELD_PRICE_ID,
                'integer',
                [
                    'null' => false,
                ],
            )
            ->addColumn(
                Tables\SnackSells::FIELD_SOLD_AT,
                'datetime',
                [
                    'null' => false,
                ],
            )
            ->addForeignKey(
                Tables\SnackSells::FIELD_PRICE_ID,
                Tables::PRICES_HISTORY,
                Tables\PricesHistory::FIELD_PRICE_ID,
                [
                    'update' => 'CASCADE',
                    'delete' => 'NO_ACTION',
                ],
            )
            ->create()
        ;
    }
}
