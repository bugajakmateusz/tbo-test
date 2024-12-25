<?php

declare(strict_types=1);

namespace Polsl\Application\Command\UpdateSnack;

use Polsl\Domain\Model\Snack\Name;
use Polsl\Domain\Model\Snack\SnackRepositoryInterface;

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
