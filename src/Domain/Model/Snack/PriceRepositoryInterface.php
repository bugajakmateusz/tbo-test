<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Snack;

use Tab\Domain\Model\Machine\Machine;

interface PriceRepositoryInterface
{
    public function add(Price $price): void;

    public function getActualPrice(Machine $machine, Snack $snack): Price;
}
