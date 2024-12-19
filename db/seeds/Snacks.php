<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;
use Tab\Packages\Constants\Database\Tables;
use Tab\Packages\Faker\Faker;

final class Snacks extends AbstractSeed
{
    public function run(): void
    {
        $snacks = [];
        for ($i = 0; $i < 80; ++$i) {
            $snacks[] = [
                'name' => Faker::words(3),
            ];
        }

        $table = $this->table(Tables::SNACKS);
        $table->insert($snacks);
        $table->saveData();
    }

    /** @return string[] */
    public function getDependencies(): array
    {
        return ['ClearDatabase'];
    }
}
