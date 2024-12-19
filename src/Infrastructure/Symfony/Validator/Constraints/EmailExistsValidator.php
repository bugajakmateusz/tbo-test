<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Tab\Application\ValidatorQuery\EmailExistsQueryInterface;
use Tab\Domain\Email;

final class EmailExistsValidator extends ConstraintValidator
{
    public function __construct(private readonly EmailExistsQueryInterface $emailExistsQuery)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EmailExists) {
            throw new UnexpectedTypeException($constraint, EmailExists::class);
        }

        if (empty($value)) {
            return;
        }

        if (false === \is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        try {
            $email = Email::fromString($value);
        } catch (\Throwable) {
            return;
        }

        $isEmailExists = $this->emailExistsQuery
            ->query($email->toString())
        ;

        if ($isEmailExists) {
            $violationBuilder = $this->context
                ->buildViolation($constraint->message)
            ;
            $violationBuilder->addViolation();
        }
    }
}
