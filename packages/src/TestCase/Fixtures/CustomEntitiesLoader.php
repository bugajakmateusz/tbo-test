<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Fixtures;

use Doctrine\DBAL\Connection;
use Tab\Packages\Constants\Database\Tables;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;
use Tab\Packages\TestCase\Fixtures\Entity\Machine;
use Tab\Packages\TestCase\Fixtures\Entity\MachineSnack;
use Tab\Packages\TestCase\Fixtures\Entity\Snack;
use Tab\Packages\TestCase\Fixtures\Entity\User;

final class CustomEntitiesLoader
{
    private const PURGE_TABLES = [
        Tables::MACHINE_SNACKS,
        Tables::USERS,
        Tables::MACHINES,
        Tables::SNACKS,
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

        $snacks = $this->filterObjects(Snack::class, ...$entities);
        $this->addSnacks(...$snacks);

        $machineSnacks = $this->filterObjects(MachineSnack::class, ...$entities);
        $this->addMachineSnacks(...$machineSnacks);
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

    private function addSnacks(Snack ...$snacks): void
    {
        foreach ($snacks as $snack) {
            $addedSnacks = $this->connection
                ->insert(
                    Tables::SNACKS,
                    [
                        Tables\Snacks::FIELD_SNACK_ID => $snack->id,
                        Tables\Snacks::FIELD_NAME => $snack->name,
                    ],
                )
            ;

            if (1 !== $addedSnacks) {
                throw new \RuntimeException('Unable to add new snack.');
            }
        }
    }

    private function addMachineSnacks(MachineSnack ...$machineSnacks): void
    {
        foreach ($machineSnacks as $machineSnack) {
            $machine = $machineSnack->machine;
            $snack = $machineSnack->snack;
            $addedMachineSnacks = $this->connection
                ->insert(
                    Tables::MACHINE_SNACKS,
                    [
                        Tables\MachineSnack::FIELD_ID => $machineSnack->id,
                        Tables\MachineSnack::FIELD_MACHINE_ID => $machine->id,
                        Tables\MachineSnack::FIELD_SNACK_ID => $snack->id,
                        Tables\MachineSnack::FIELD_QUANTITY => $machineSnack->quantity,
                        Tables\MachineSnack::FIELD_POSITION => $machineSnack->position,
                    ],
                )
            ;

            if (1 !== $addedMachineSnacks) {
                throw new \RuntimeException('Unable to add new machine snack.');
            }
        }
    }
}
