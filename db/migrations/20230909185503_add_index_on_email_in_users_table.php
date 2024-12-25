<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Polsl\Packages\Constants\Database\Tables;

final class AddIndexOnEmailInUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table(Tables::USERS);
        $table->addIndex(
            Tables\Users::FIELD_EMAIL,
            ['unique' => true],
        );
        $table->update();
    }
}
