<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;
use Polsl\Packages\Constants\Database\Tables;
use Polsl\Packages\Faker\Faker;

final class WarehouseSnacks extends AbstractSeed
{
    public function run(): void
    {
        $snacks = $this->fetchAll(
            <<<'SQL'
                SELECT snack_id
                FROM snacks
                SQL
        );
        $snacksIds = \array_column(
            $snacks,
            'snack_id',
        );
        $warehouseSnacks = [];

        foreach ($snacksIds as $snackId) {
            $warehouseSnacks[] = [
                'snack_id' => $snackId,
                'quantity' => Faker::int(0, 1000),
            ];
        }

        $table = $this->table(Tables::WAREHOUSE_SNACKS);
        $table->insert($warehouseSnacks);
        $table->saveData();
    }

    /** @return string[] */
    public function getDependencies(): array
    {
        return ['ClearDatabase', 'Snacks'];
    }
}
