<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Machine;

class Machine
{
    private readonly int $id;

    private function __construct(
        private string $location,
        private int $positionNumber,
        private int $positionCapacity,
    ) {
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
}
