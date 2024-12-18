<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Snack;

interface SnackRepositoryInterface
{
    public function add(
        Snack $snack,
    ): void;
}
