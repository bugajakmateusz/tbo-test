<?php

declare(strict_types=1);

namespace Polsl\Packages\Validator;

use Symfony\Component\Validator\ConstraintViolationInterface;

final readonly class SymfonyViolation implements ViolationInterface
{
    public function __construct(private ConstraintViolationInterface $constraintViolation) {}

    public function propertyPath(): string
    {
        return $this->constraintViolation
            ->getPropertyPath()
        ;
    }

    public function message(): string
    {
        return (string) $this->constraintViolation
            ->getMessage()
        ;
    }
}
