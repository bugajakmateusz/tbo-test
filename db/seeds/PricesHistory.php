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
        $machineSnacksData = $this->fetchAll(
            <<<'SQL'
                SELECT DISTINCT machine_id, snack_id
                FROM machine_snacks
                SQL
        );
        $pricesHistory = [];

        foreach ($machineSnacksData as $machineSnackData) {
            $machineId = $machineSnackData['machine_id'];
            $snackId = $machineSnackData['snack_id'];
            for ($i = 0; $i < Faker::int(1, 5); ++$i) {
                $pricesHistory[] = $this->createData($machineId, $snackId);
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
