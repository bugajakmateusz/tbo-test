<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class EmailExists extends Constraint
{
    public string $message = 'email.already_exist';

    public function validatedBy(): string
    {
        return EmailExistsValidator::class;
    }
}
