<?php

declare(strict_types=1);

namespace Polsl\Application\Command\UpdateMachine;

use Polsl\Domain\Model\Machine\Location;
use Polsl\Domain\Model\Machine\Machine;
use Polsl\Domain\Model\Machine\MachineRepositoryInterface;

final readonly class UpdateMachineHandler
{
    private const FIELD_LOCATION = 'location';
    private const FIELD_POSITIONS_NUMBER = 'positionsNumber';
    private const FIELD_POSITIONS_CAPACITY = 'positionsCapacity';

    public function __construct(
        private MachineRepositoryInterface $machineRepository,
    ) {}

    public function __invoke(UpdateMachine $command): void
    {
        $changes = $this->commandChangesToArray($command);
        $nonEmptyChanges = \array_filter($changes, static fn (mixed $value): bool => null !== $value);
        if ([] === $nonEmptyChanges) {
            return;
        }
        $machine = $this->machineRepository
            ->get($command->id)
        ;
        foreach ($nonEmptyChanges as $field => $value) {
            match ($field) {
                self::FIELD_LOCATION => $this->changeLocation($machine, $value),
                self::FIELD_POSITIONS_NUMBER => $machine->changePositionNumber($value),
                self::FIELD_POSITIONS_CAPACITY => $machine->changePositionCapacity($value),
            };
        }
    }

    /**
     * @return array{
     *     location: string|null,
     *     positionsNumber: int|null,
     *     positionsCapacity: int|null,
     * }
     */
    private function commandChangesToArray(UpdateMachine $command): array
    {
        return [
            self::FIELD_LOCATION => $command->location,
            self::FIELD_POSITIONS_NUMBER => $command->positionsNumber,
            self::FIELD_POSITIONS_CAPACITY => $command->positionsCapacity,
        ];
    }

    private function changeLocation(
        Machine $machine,
        string $location,
    ): void {
        $newLocation = Location::fromString($location);

        $machine->changeLocation($newLocation);
    }
}
