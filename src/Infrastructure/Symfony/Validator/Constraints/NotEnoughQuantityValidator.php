<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Tab\Application\Command\AddNewMachineSnack\AddNewMachineSnack;
use Tab\Application\ValidatorQuery\NotEnoughQuantityQueryInterface;

final class NotEnoughQuantityValidator extends ConstraintValidator
{
    public function __construct(private readonly NotEnoughQuantityQueryInterface $notEnoughQuantityQuery) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotEnoughQuantity) {
            throw new UnexpectedTypeException($constraint, NotEnoughQuantity::class);
        }

        if (empty($value)) {
            return;
        }

        if (!$value instanceof AddNewMachineSnack) {
            throw new UnexpectedValueException($value, 'AddNewMachineSnack');
        }

        $isTooLowProduct = $this->notEnoughQuantityQuery
            ->query($value->quantity, $value->snackId)
        ;

        if ($isTooLowProduct) {
            $violationBuilder = $this->context
                ->buildViolation($constraint->message)
                ->atPath('quantity')
            ;
            $violationBuilder->addViolation();
        }
    }
}
