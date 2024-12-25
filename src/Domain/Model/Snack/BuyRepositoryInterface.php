<?php

declare(strict_types=1);

namespace Polsl\Domain\Model\Snack;

interface BuyRepositoryInterface
{
    public function add(Buy $buy): void;
}
