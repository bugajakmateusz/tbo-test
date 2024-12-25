<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;
use Polsl\Packages\Constants\Database\Tables;
use Polsl\Packages\Faker\Faker;

final class Machines extends AbstractSeed
{
    public function run(): void
    {
        $machines = [];
        for ($i = 0; $i < 80; ++$i) {
            $machines[] = $this->createMachineData();
        }

        $table = $this->table(Tables::MACHINES);
        $table->insert($machines);
        $table->saveData();
    }

    /** @return string[] */
    public function getDependencies(): array
    {
        return ['ClearDatabase'];
    }

    /**
     * @return array{
     *     location: string,
     *     positions_no: int,
     *     positions_capacity: int,
     * }
     */
    private function createMachineData(): array
    {
        return [
            'location' => Faker::address(),
            'positions_no' => Faker::intId(),
            'positions_capacity' => Faker::intId(),
        ];
    }
}
