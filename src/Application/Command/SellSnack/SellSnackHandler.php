<?php

declare(strict_types=1);

namespace Tab\Application\Command\SellSnack;

use Tab\Domain\Model\Machine\MachineSnackRepositoryInterface;
use Tab\Domain\Model\Snack\PriceRepositoryInterface;
use Tab\Domain\Model\Snack\SnackSellRepositoryInterface;
use Tab\Domain\Service\ClockInterface;

final readonly class SellSnackHandler
{
    public function __construct(
        private PriceRepositoryInterface $priceRepository,
        private ClockInterface $clock,
        private SnackSellRepositoryInterface $snackSellRepository,
        private MachineSnackRepositoryInterface $machineSnackRepository,
    ) {}

    public function __invoke(SellSnack $command): void
    {
        $machineSnack = $this->machineSnackRepository
            ->getByPosition(
                $command->machineId,
                $command->snackId,
                $command->position,
            )
        ;

        $machineSnack->sellSnack(
            $this->priceRepository,
            $this->clock,
            $this->snackSellRepository,
        );
    }
}
