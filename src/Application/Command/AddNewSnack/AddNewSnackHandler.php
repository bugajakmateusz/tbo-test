<?php

declare(strict_types=1);

namespace Tab\Application\Command\AddNewSnack;

use Tab\Domain\Model\Snack\Name;
use Tab\Domain\Model\Snack\Snack;
use Tab\Domain\Model\Snack\SnackRepositoryInterface;

final readonly class AddNewSnackHandler
{
    public function __construct(
        private SnackRepositoryInterface $snackRepository,
    ) {}

    public function __invoke(
        AddNewSnack $command,
    ): void {
        $name = Name::fromString(
            $command->name,
        );
        $snack = Snack::create(
            $name,
            $this->snackRepository,
        );

        $this->snackRepository
            ->add($snack)
        ;
    }
}
