<?php

declare(strict_types=1);

namespace Tab\Application\Command\UpdateSnack;

use Tab\Domain\Model\Snack\Name;
use Tab\Domain\Model\Snack\SnackRepositoryInterface;

final readonly class UpdateSnackHandler
{
    public function __construct(
        private SnackRepositoryInterface $snackRepository,
    ) {}

    public function __invoke(
        UpdateSnack $command,
    ): void {
        $snack = $this->snackRepository
            ->get($command->id)
        ;
        $snack->changeName(
            Name::fromString(
                $command->name,
            ),
        );
    }
}
