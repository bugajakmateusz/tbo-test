<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Tab\Packages\Constants\Database\Tables;

final class AddSnacksTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table(
            Tables::SNACKS,
            [
                'id' => Tables\Snacks::FIELD_SNACK_ID,
            ],
        );
        $table->addColumn(
            Tables\Snacks::FIELD_NAME,
            'string',
            [
                'null' => false,
                'limit' => 255,
            ],
        );
        $table->create();
    }
}
