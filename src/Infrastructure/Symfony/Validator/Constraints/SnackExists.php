<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class SnackExists extends Constraint
{
    public string $message = 'snack.already_exist';

    public function validatedBy(): string
    {
        return SnackExistsValidator::class;
    }
}
