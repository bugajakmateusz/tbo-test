<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Snack;

interface SnackSellRepositoryInterface
{
    public function add(SnackSell $snackSell): void;
}
