<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Tab\Packages\Constants\Database\Tables;

final class CreateWarehouseSnacksTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table(
            Tables::WAREHOUSE_SNACKS,
            [
                'id' => false,
                'primary_key' => Tables\WarehouseSnacks::FIELD_SNACK_ID,
            ],
        );

        $table
            ->addColumn(
                Tables\WarehouseSnacks::FIELD_SNACK_ID,
                'integer',
                [
                    'null' => false,
                ],
            )
            ->addColumn(
                Tables\WarehouseSnacks::FIELD_QUANTITY,
                'integer',
                [
                    'null' => false,
                    'limit' => 10000,
                ],
            )
            ->addForeignKey(
                Tables\WarehouseSnacks::FIELD_SNACK_ID,
                Tables::SNACKS,
                Tables\Snacks::FIELD_SNACK_ID,
                [
                    'update' => 'CASCADE',
                    'delete' => 'NO_ACTION',
                ],
            )
            ->create()
        ;
    }
}
