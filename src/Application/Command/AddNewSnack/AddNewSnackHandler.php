<?php

declare(strict_types=1);

namespace Polsl\Application\Command\AddNewSnack;

use Polsl\Domain\Model\Snack\Name;
use Polsl\Domain\Model\Snack\Snack;
use Polsl\Domain\Model\Snack\SnackRepositoryInterface;

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
        $snack = Snack::create($name);

        $this->snackRepository
            ->add($snack)
        ;
    }
}
