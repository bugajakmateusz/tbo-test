<?php

declare(strict_types=1);

namespace Tab\Application\Command\UpdateSnackPrice;

use Tab\Domain\Model\Machine\MachineRepositoryInterface;
use Tab\Domain\Model\Snack\Price;
use Tab\Domain\Model\Snack\PriceRepositoryInterface;
use Tab\Domain\Model\Snack\SnackRepositoryInterface;
use Tab\Domain\Service\ClockInterface;

final readonly class UpdateSnackPriceHandler
{
    public function __construct(
        private MachineRepositoryInterface $machineRepository,
        private SnackRepositoryInterface $snackRepository,
        private PriceRepositoryInterface $priceRepository,
        private ClockInterface $clock,
    ) {}

    public function __invoke(UpdateSnackPrice $command): void
    {
        $machine = $this->machineRepository
            ->get($command->machineId)
        ;
        $snack = $this->snackRepository
            ->get($command->snackId)
        ;

        $price = Price::create(
            $machine,
            $snack,
            $command->price,
            $this->clock,
        );

        $this->priceRepository
            ->add($price)
        ;
    }
}
