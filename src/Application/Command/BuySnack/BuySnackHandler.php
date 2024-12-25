<?php

declare(strict_types=1);

namespace Polsl\Application\Command\BuySnack;

use Polsl\Domain\Model\Snack\Buy;
use Polsl\Domain\Model\Snack\BuyRepositoryInterface;
use Polsl\Domain\Model\Snack\SnackRepositoryInterface;
use Polsl\Domain\Service\ClockInterface;

final readonly class BuySnackHandler
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
    }
}
