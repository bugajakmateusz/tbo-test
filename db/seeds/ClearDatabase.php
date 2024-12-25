<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;
use Polsl\Packages\Constants\Database\Tables;

final class ClearDatabase extends AbstractSeed
{
    public function run(): void
    {
        $this->deleteTableData(Tables::USERS);
        $this->deleteTableData(Tables::SNACKS);
        $this->deleteTableData(Tables::MACHINES);
        $this->deleteTableData(Tables::MACHINE_SNACKS);
        $this->deleteTableData(Tables::PRICES_HISTORY);
        $this->deleteTableData(Tables::WAREHOUSE_SNACKS);
    }

    private function deleteTableData(string $table): void
    {
        $this->execute("ALTER TABLE {$table} DISABLE TRIGGER ALL;");
        $this->execute("DELETE FROM {$table};");
        $this->execute("ALTER TABLE {$table} ENABLE TRIGGER ALL;");
    }
}
