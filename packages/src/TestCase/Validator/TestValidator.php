<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;
use Tab\Packages\Validator\SymfonyViolation;
use Tab\Packages\Validator\ViolationInterface;
use Tab\Packages\Validator\Violations;

final readonly class TestValidator
{
    public function __construct(private SymfonyValidatorInterface $validator) {}

    /**
     * @param null|Constraint|Constraint[]     $constraints
     * @param null|array<GroupSequence|string> $validationGroups
     */
    public function validate(
        mixed $data,
        Constraint|array $constraints = null,
        ?array $validationGroups = null,
    ): Violations {
        $violations = $this->validator
            ->validate(
                $data,
                $constraints,
                $validationGroups,
            )
        ;

        return Violations::fromViolations(
            ...\array_map(
                static function (ConstraintViolationInterface $constraintViolation): ViolationInterface {
                    return new SymfonyViolation($constraintViolation);
                },
                \iterator_to_array($violations),
            ),
        );
    }
}
