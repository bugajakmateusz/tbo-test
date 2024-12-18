<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Tab\Packages\Constants\Database\Tables;

final class AddMachinesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table(
            Tables::MACHINES,
            [
                'id' => Tables\Machines::FIELD_MACHINE_ID,
            ],
        );
        $table
            ->addColumn(
                Tables\Machines::FIELD_LOCATION,
                'string',
                [
                    'null' => false,
                    'limit' => 255,
                ],
            )
            ->addColumn(
                Tables\Machines::FIELD_POSITIONS_NUMBER,
                'integer',
                [
                    'null' => false,
                    'signed' => false,
                ],
            )
            ->addColumn(
                Tables\Machines::FIELD_POSITIONS_CAPACITY,
                'integer',
                [
                    'null' => false,
                    'signed' => false,
                ],
            )
        ;
        $table->create();
    }
}
