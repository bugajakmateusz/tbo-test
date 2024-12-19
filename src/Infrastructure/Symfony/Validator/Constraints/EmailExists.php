<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class EmailExists extends Constraint
{
    public string $message = 'email.already_exist';

    public function validatedBy(): string
    {
        return EmailExistsValidator::class;
    }
}
