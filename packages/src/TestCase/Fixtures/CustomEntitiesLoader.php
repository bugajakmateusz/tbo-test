<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Fixtures;

use Doctrine\DBAL\Connection;
use Tab\Packages\Constants\Database\Tables;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;
use Tab\Packages\TestCase\Fixtures\Entity\Machine;
use Tab\Packages\TestCase\Fixtures\Entity\User;

final class CustomEntitiesLoader
{
    private const PURGE_TABLES = [
        Tables::USERS,
        Tables::MACHINES,
    ];

    public function __construct(
        private readonly Connection $connection,
        private readonly JsonSerializerInterface $jsonSerializer,
    ) {
    }

    public function load(bool $append = false, object ...$entities): void
    {
        if (!$append) {
            $this->purgeTables();
        }

        if (0 === \count($entities)) {
            return;
        }

        $users = $this->filterObjects(User::class, ...$entities);
        $this->addUsers(...$users);

        $machines = $this->filterObjects(Machine::class, ...$entities);
        $this->addMachines(...$machines);
    }

    public function purgeTables(): void
    {
        foreach (self::PURGE_TABLES as $table) {
            $statement = "DELETE FROM {$table}";

            $this->connection
                ->executeStatement($statement)
            ;
        }
    }

    /**
     * @template T
     *
     * @param class-string<T> $class
     *
     * @return T[]
     */
    private function filterObjects(string $class, object ...$objects): array
    {
        return \array_filter(
            $objects,
            static fn (object $object): bool => $object instanceof $class,
        );
    }

    private function addUsers(User ...$users): void
    {
        foreach ($users as $user) {
            $rolesJson = $this->jsonSerializer
                ->encode($user->roles)
            ;

            $addedUsers = $this->connection
                ->insert(
                    Tables::USERS,
                    [
                        Tables\Users::FIELD_USER_ID => $user->id,
                        Tables\Users::FIELD_NAME => $user->name,
                        Tables\Users::FIELD_SURNAME => $user->surname,
                        Tables\Users::FIELD_EMAIL => $user->email,
                        Tables\Users::FIELD_PASSWORD_HASH => $user->passwordHash,
                        Tables\Users::FIELD_ROLES => $rolesJson,
                    ],
                )
            ;

            if (1 !== $addedUsers) {
                throw new \RuntimeException('Unable to add new user.');
            }
        }
    }

    private function addMachines(Machine ...$machines): void
    {
        foreach ($machines as $machine) {
            $addedMachines = $this->connection
                ->insert(
                    Tables::MACHINES,
                    [
                        Tables\Machines::FIELD_MACHINE_ID => $machine->id,
                        Tables\Machines::FIELD_LOCATION => $machine->location,
                        Tables\Machines::FIELD_POSITIONS_NUMBER => $machine->positionNo,
                        Tables\Machines::FIELD_POSITIONS_CAPACITY => $machine->positionCapacity,
                    ],
                )
            ;

            if (1 !== $addedMachines) {
                throw new \RuntimeException('Unable to add new machine.');
            }
        }
    }
}
