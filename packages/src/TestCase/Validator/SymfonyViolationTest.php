<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase\Validator;

use Polsl\Packages\Validator\SymfonyViolation;
use Polsl\Packages\Validator\ViolationInterface;
use Symfony\Component\Validator\ConstraintViolation;

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
