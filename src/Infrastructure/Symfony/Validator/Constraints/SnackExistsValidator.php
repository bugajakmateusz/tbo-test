<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Tab\Application\ValidatorQuery\SnackExistsQueryInterface;
use Tab\Domain\Model\Snack\Snack;
use Tab\Domain\Model\Snack\Name;

final class SnackExistsValidator extends ConstraintValidator
{
    public function __construct(private readonly SnackExistsQueryInterface $snackExistsQuery) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof SnackExists) {
            throw new UnexpectedTypeException($constraint, SnackExists::class);
        }

        if (empty($value)) {
            return;
        }

        if (false === \is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        try {
            $name = Name::fromString($value);
            $snack = Snack::create($name);
        } catch (\Throwable) {
            return;
        }

        $isSnackExists = $this->snackExistsQuery
            ->query($snack->toString())
        ;

        if ($isSnackExists) {
            $violationBuilder = $this->context
                ->buildViolation($constraint->message)
            ;
            $violationBuilder->addViolation();
        }
    }
}
