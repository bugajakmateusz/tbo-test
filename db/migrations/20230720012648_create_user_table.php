<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Polsl\Packages\Constants\Database\Tables;

final class CreateUserTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table(
            Tables::USERS,
            [
                'id' => Tables\Users::FIELD_USER_ID,
            ],
        );
        $table
            ->addColumn(
                Tables\Users::FIELD_EMAIL,
                'string',
                [
                    'null' => false,
                    'limit' => 255,
                ],
            )
            ->addColumn(
                Tables\Users::FIELD_NAME,
                'string',
                [
                    'null' => false,
                    'limit' => 255,
                ],
            )
            ->addColumn(
                Tables\Users::FIELD_SURNAME,
                'string',
                [
                    'null' => false,
                    'limit' => 255,
                ],
            )
            ->addColumn(
                Tables\Users::FIELD_PASSWORD_HASH,
                'string',
                [
                    'null' => false,
                    'limit' => 255,
                ],
            )
            ->addColumn(
                Tables\Users::FIELD_ROLES,
                'json',
                [
                    'null' => false,
                ],
            )
        ;
        $table->create();
    }
}
