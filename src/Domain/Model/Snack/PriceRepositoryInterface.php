<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Snack;

interface PriceRepositoryInterface
{
    public function add(Price $price): void;
}
