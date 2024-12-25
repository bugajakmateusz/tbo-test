<?php

declare(strict_types=1);

namespace Polsl\Domain\Model\Machine;

use Polsl\Domain\DomainException;

class Machine
{
    private int $id;

    /** @var array<MachineSnack>|\Traversable<MachineSnack> */
    private iterable $snacks;

    private function __construct(
        private string $location,
        private int $positionNumber,
        private int $positionCapacity,
    ) {
        $this->snacks = [];
    }

    public static function create(
        Location $location,
        int $positionNumber,
        int $positionCapacity,
    ): self {
        return new self(
            $location->toString(),
            $positionNumber,
            $positionCapacity,
        );
    }

    public function changeLocation(Location $location): void
    {
        $this->location = $location->toString();
    }

    public function changePositionNumber(int $positionNumber): void
    {
        $this->positionNumber = $positionNumber;
    }

    public function changePositionCapacity(int $positionCapacity): void
    {
        $this->positionCapacity = $positionCapacity;
    }

    public function addSnack(
        MachineSnack $snack,
    ): void {
        $newSnackPosition = $snack->position();
        $samePosition = \array_filter(
            \iterator_to_array($this->snacks),
            static function (MachineSnack $machineSnack) use ($newSnackPosition): bool {
                return 0 !== $machineSnack->quantity() && $machineSnack->position() === $newSnackPosition;
            },
        );

        if ([] !== $samePosition) {
            throw new DomainException('Snack on provided position already placed');
        }

        $snacks[] = $snack;

        $this->snacks = $snacks;
    }

    public function id(): int
    {
        return $this->id;
    }
}
