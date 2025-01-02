<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;
use Polsl\Packages\Constants\Database\Tables;
use Polsl\Packages\Constants\Hasher;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\PasswordHasher\SymfonySodiumPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\SodiumPasswordHasher;

final class Users extends AbstractSeed
{
    public function run(): void
    {
        $users = [];
        for ($i = 0; $i < 10; ++$i) {
            $users[] = $this->createUserData();
        }

        $table = $this->table(Tables::USERS);
        $table->insert($users);
        $table->saveData();
    }

    /** @return string[] */
    public function getDependencies(): array
    {
        return ['ClearDatabase'];
    }

    /**
     * @param string[] $roles
     *
     * @return array{
     *     name: string,
     *     surname: string,
     *     email: string,
     *     pass_hash: string,
     *     roles: string,
     * }
     */
    private function createUserData(
        array $roles = [],
        string $password = 'polsl-admin',
    ): array {
        $passwordHasher = new SymfonySodiumPasswordHasher(
            new SodiumPasswordHasher(
                Hasher::TEST_SODIUM_TIME_COST,
                Hasher::TEST_SODIUM_MEMORY_LIMIT_IN_BYTES,
            ),
        );
        $roles[] = 'ROLE_USER';
        $uniqueRoles = \array_unique($roles);

        return [
            'name' => Faker::firstName(),
            'surname' => Faker::lastName(),
            'email' => Faker::email(),
            'pass_hash' => $passwordHasher->hash($password),
            'roles' => \json_encode($uniqueRoles, \JSON_THROW_ON_ERROR),
        ];
    }
}
