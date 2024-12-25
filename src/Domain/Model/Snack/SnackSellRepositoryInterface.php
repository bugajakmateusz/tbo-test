<?php

declare(strict_types=1);

namespace Polsl\Domain\Model\Snack;

interface SnackSellRepositoryInterface
{
    public function add(SnackSell $snackSell): void;
}
