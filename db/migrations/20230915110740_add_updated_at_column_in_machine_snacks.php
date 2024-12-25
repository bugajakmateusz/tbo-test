<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Tab\Packages\Constants\Database\Tables;

final class AddUpdatedAtColumnInMachineSnacks extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table(Tables::MACHINE_SNACKS);
        $table->addColumn(
            Tables\MachineSnacks::FIELD_UPDATED_AT,
            'datetime',
            [
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
        );
        $table->update();
    }
}
