<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Validator;

use Symfony\Component\Validator\ConstraintViolation;
use Tab\Packages\Validator\SymfonyViolation;
use Tab\Packages\Validator\ViolationInterface;

/** @internal */
final class SymfonyViolationTest extends AbstractViolationTestCase
{
    public function createViolation(string $propertyPath, string $message): ViolationInterface
    {
        $violation = new ConstraintViolation(
            $message,
            $message,
            [],
            null,
            $propertyPath,
            '',
        );

        return new SymfonyViolation($violation);
    }
}
