<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class MachinePositionTaken extends Constraint
{
    public string $message = 'machine.position_taken';

    public function validatedBy(): string
    {
        return MachinePositionTakenValidator::class;
    }

    public function getTargets(): string
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}
