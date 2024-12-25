<?php

declare(strict_types=1);

namespace Polsl\Application\Command\UpdateSnackPrice;

use Polsl\Domain\Model\Machine\MachineRepositoryInterface;
use Polsl\Domain\Model\Snack\Price;
use Polsl\Domain\Model\Snack\PriceRepositoryInterface;
use Polsl\Domain\Model\Snack\SnackRepositoryInterface;
use Polsl\Domain\Service\ClockInterface;

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
