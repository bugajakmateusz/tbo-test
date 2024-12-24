<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class NotEnoughQuantity extends Constraint
{
    public string $message = 'warehouse.too_low_product';

    public function validatedBy(): string
    {
        return NotEnoughQuantityValidator::class;
    }

    public function getTargets(): string
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}
