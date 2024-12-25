<?php

declare(strict_types=1);

namespace Polsl\Domain\Model\Snack;

interface SnackRepositoryInterface
{
    public function get(
        int $snackId,
    ): Snack;

    public function add(
        Snack $snack,
    ): void;
}
