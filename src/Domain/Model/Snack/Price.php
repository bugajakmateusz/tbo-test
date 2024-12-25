<?php

declare(strict_types=1);

namespace Polsl\Domain\Model\Snack;

use Polsl\Domain\DomainException;
use Polsl\Domain\Model\Machine\Machine;
use Polsl\Domain\Service\ClockInterface;

class Price
{
    private int $id;

    private function __construct(
        private int $machineId,
        private int $snackId,
        private float $price,
        private \DateTimeImmutable $createdAt,
    ) {}

    public static function create(
        Machine $machine,
        Snack $snack,
        float $price,
        ClockInterface $clock,
    ): self {
        if ($price <= 0) {
            throw new DomainException('Price cannot be lower than or equal 0.');
        }

        return new self(
            $machine->id(),
            $snack->id(),
            $price,
            $clock->now(),
        );
    }

    public function id(): int
    {
        return $this->id;
    }
}
