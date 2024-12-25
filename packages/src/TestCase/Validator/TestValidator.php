<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase\Validator;

use Polsl\Packages\Validator\SymfonyViolation;
use Polsl\Packages\Validator\ViolationInterface;
use Polsl\Packages\Validator\Violations;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

final readonly class TestValidator
{
    public function __construct(private SymfonyValidatorInterface $validator) {}

    /**
     * @param null|Constraint|Constraint[]     $constraints
     * @param null|array<GroupSequence|string> $validationGroups
     */
    public function validate(
        mixed $data,
        null|array|Constraint $constraints = null,
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
