<?php

declare(strict_types=1);

namespace Polsl\Application\Command\SellSnack;

use Polsl\Domain\Model\Machine\MachineSnackRepositoryInterface;
use Polsl\Domain\Model\Snack\PriceRepositoryInterface;
use Polsl\Domain\Model\Snack\SnackSellRepositoryInterface;
use Polsl\Domain\Service\ClockInterface;

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
