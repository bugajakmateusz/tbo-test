<?php

declare(strict_types=1);

namespace Polsl\Packages\Validator;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

final readonly class SymfonyValidator implements ValidatorInterface
{
    public function __construct(private SymfonyValidatorInterface $validator) {}

    public function validate(object $object, ?array $validationGroups = null): Violations
    {
        $violations = $this->validator
            ->validate(
                $object,
                null,
                $validationGroups,
            )
        ;

        return Violations::fromViolations(
            ...\array_map(
                static fn (ConstraintViolationInterface $constraintViolation): ViolationInterface => new SymfonyViolation($constraintViolation),
                \iterator_to_array($violations),
            ),
        );
    }
}
