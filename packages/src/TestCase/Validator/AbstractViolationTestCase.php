<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase\Validator;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\UnitTestCase;
use Polsl\Packages\Validator\ViolationInterface;

abstract class AbstractViolationTestCase extends UnitTestCase
{
    abstract public function createViolation(string $propertyPath, string $message): ViolationInterface;

    public function test_property_path_and_message(): void
    {
        // Arrange
        $propertyPath = Faker::words(1);
        $message = Faker::words(5);

        // Act
        $violation = $this->createViolation($propertyPath, $message);

        // Assert
        self::assertSame($propertyPath, $violation->propertyPath());
        self::assertSame($message, $violation->message());
    }
}
