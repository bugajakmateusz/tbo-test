<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;
use Tab\Packages\Constants\Database\Tables;
use Tab\Packages\Constants\Date;
use Tab\Packages\Faker\Faker;

final class PricesHistory extends AbstractSeed
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
        $pricesHistory = [];

        foreach ($machinesIds as $machineId) {
            for ($i = 0; $i < Faker::int(0, 5); ++$i) {
                $snackId = Faker::randomElement($snacksIds);
                for ($i = 0; $i < Faker::int(1, 3); ++$i) {
                    $pricesHistory[] = $this->createData($machineId, $snackId);
                }
            }
        }
        \usort(
            $pricesHistory,
            static fn (array $left, array $right): int => $left['price_created_at'] <=> $right['price_created_at'],
        );

        $table = $this->table(Tables::PRICES_HISTORY);
        $table->insert($pricesHistory);
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
     *     price: int,
     *     price_created_at: string,
     * }
     */
    private function createData(int $machineId, int $snackId): array
    {
        $createdAt = Faker::dateTimeBetween();

        return [
            'snack_id' => $snackId,
            'machine_id' => $machineId,
            'price' => Faker::int(0, 1000),
            'price_created_at' => $createdAt->format(Date::SQL_DATE_TIME_FORMAT),
        ];
    }
}
