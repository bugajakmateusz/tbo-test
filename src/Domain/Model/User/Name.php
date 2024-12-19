<?php

declare(strict_types=1);

namespace Tab\Domain\Model\User;

use Tab\Domain\SanitizedString;

class Name
{
    public const MAX_LENGTH = 20;

    private function __construct(private readonly string $name)
    {
    }

    public static function fromString(string $name): self
    {
        $sanitizedName = SanitizedString::create($name);
        $sanitizedName->checkIsEmpty('Name');
        $sanitizedName->checkMaxLength(self::MAX_LENGTH, 'Name');

        return new self($sanitizedName->toString());
    }

    public function toString(): string
    {
        return $this->name;
    }
}
