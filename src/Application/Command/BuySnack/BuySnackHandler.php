<?php

declare(strict_types=1);

namespace Tab\Application\Command\BuySnack;

use Tab\Domain\Model\Snack\Buy;
use Tab\Domain\Model\Snack\BuyRepositoryInterface;
use Tab\Domain\Model\Snack\SnackRepositoryInterface;
use Tab\Domain\Service\ClockInterface;

final class BuySnackHandler
{
    public function __construct(
        private SnackRepositoryInterface $snackRepository,
        private BuyRepositoryInterface $buyRepository,
        private ClockInterface $clock,
    ) {}

    public function __invoke(BuySnack $command): void
    {
        $snack = $this->snackRepository
            ->get($command->snackId)
        ;

        $buy = Buy::create(
            $snack,
            $this->clock,
            $command->price,
            $command->quantity,
        );

        $this->buyRepository
            ->add($buy)
        ;

        $snack->addWarehouseQuantity($command->quantity);
    }
}
