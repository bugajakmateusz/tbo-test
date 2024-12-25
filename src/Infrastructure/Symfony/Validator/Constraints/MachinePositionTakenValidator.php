<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Symfony\Validator\Constraints;

use Polsl\Application\Command\AddNewMachineSnack\AddNewMachineSnack;
use Polsl\Application\ValidatorQuery\MachinePositionTakenQueryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class MachinePositionTakenValidator extends ConstraintValidator
{
    public function __construct(private readonly MachinePositionTakenQueryInterface $machinePositionTakenQuery) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof MachinePositionTaken) {
            throw new UnexpectedTypeException($constraint, MachinePositionTaken::class);
        }

        if (empty($value)) {
            return;
        }

        if (!$value instanceof AddNewMachineSnack) {
            throw new UnexpectedValueException($value, 'AddNewMachineSnack');
        }

        $isPositionTaken = $this->machinePositionTakenQuery
            ->query($value->position, $value->machineId)
        ;

        if ($isPositionTaken) {
            $violationBuilder = $this->context
                ->buildViolation($constraint->message)
                ->atPath('position')
            ;
            $violationBuilder->addViolation();
        }
    }
}
