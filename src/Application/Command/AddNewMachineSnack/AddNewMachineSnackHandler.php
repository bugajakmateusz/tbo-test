<?php

declare(strict_types=1);

namespace Tab\Application\Command\AddNewMachineSnack;

use Tab\Domain\Model\Machine\MachineRepositoryInterface;
use Tab\Domain\Model\Machine\MachineSnack;
use Tab\Domain\Model\Machine\SnackPosition;
use Tab\Domain\Model\Snack\Price;
use Tab\Domain\Model\Snack\PriceRepositoryInterface;
use Tab\Domain\Model\Snack\SnackRepositoryInterface;
use Tab\Domain\Service\ClockInterface;

final class AddNewMachineSnackHandler
{
    public function __construct(
        private MachineRepositoryInterface $machineRepository,
        private SnackRepositoryInterface $snackRepository,
        private PriceRepositoryInterface $priceRepository,
        private ClockInterface $clock,
    ) {}

    public function __invoke(AddNewMachineSnack $command): void
    {
        $machine = $this->machineRepository
            ->get($command->machineId)
        ;
        $snack = $this->snackRepository
            ->get($command->snackId)
        ;

        $machineSnack = MachineSnack::create(
            $machine,
            $snack,
            $command->quantity,
            SnackPosition::fromString($command->position),
        );

        $machine->addSnack($machineSnack);

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
