<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Symfony\Validator\Constraints;

use Polsl\Application\Command\AddNewMachineSnack\AddNewMachineSnack;
use Polsl\Application\Command\UpdateMachineSnack\UpdateMachineSnack;
use Polsl\Application\ValidatorQuery\NotEnoughQuantityQueryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

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

        $isTooLowProduct = true;

        if ($value instanceof AddNewMachineSnack) {
            $isTooLowProduct = $this->notEnoughQuantityQuery
                ->queryToAdd($value->quantity, $value->snackId)
            ;
        }

        if ($value instanceof UpdateMachineSnack) {
            $isTooLowProduct = $this->notEnoughQuantityQuery
                ->queryToUpdate($value->quantity, $value->id)
            ;
        }

        if (!$value instanceof AddNewMachineSnack && !$value instanceof UpdateMachineSnack) {
            throw new UnexpectedValueException($value, 'AddNewMachineSnack or UpdateMachineSnack');
        }

        if ($isTooLowProduct) {
            $violationBuilder = $this->context
                ->buildViolation($constraint->message)
                ->atPath('quantity')
            ;
            $violationBuilder->addViolation();
        }
    }
}
