<?php

declare(strict_types=1);

namespace Polsl\Packages\Validator;

interface ViolationInterface
{
    public function propertyPath(): string;

    public function message(): string;
}
