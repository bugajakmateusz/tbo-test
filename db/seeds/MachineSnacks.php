<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;
use Polsl\Packages\Constants\Database\Tables;
use Polsl\Packages\Faker\Faker;

final class MachineSnacks extends AbstractSeed
{
    public function run(): void
    {
        $machines = $this->fetchAll(
            <<<'SQL'
                SELECT machine_id
                FROM machines
                SQL
        );
        $machinesIds = \array_column(
            $machines,
            'machine_id',
        );

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
        $machineSnacks = [];

        foreach ($machinesIds as $machineId) {
            for ($i = 0; $i < Faker::int(0, 5); ++$i) {
                $snackId = Faker::randomElement($snacksIds);
                $machineSnacks[] = $this->createData($machineId, $snackId);
            }
        }

        $table = $this->table(Tables::MACHINE_SNACKS);
        $table->insert($machineSnacks);
        $table->saveData();
    }

    /** @return string[] */
    public function getDependencies(): array
    {
        return ['ClearDatabase', 'Machines', 'Snacks'];
    }

    /**
     * @return array{
     *     snack_id: int,
     *     machine_id: int,
     *     quantity: int,
     *     position: string,
     * }
     */
    private function createData(int $machineId, int $snackId): array
    {
        return [
            'snack_id' => $snackId,
            'machine_id' => $machineId,
            'quantity' => Faker::int(0, 1000),
            'position' => Faker::hexBytes(3),
        ];
    }
}
