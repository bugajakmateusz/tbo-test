<?php

declare(strict_types=1);

namespace Polsl\Domain\Model\Machine;

use Polsl\Domain\SanitizedString;

final class Location
{
    public const MAX_LENGTH = 255;

    private function __construct(private readonly string $text) {}

    public static function fromString(string $text): self
    {
        $safeText = SanitizedString::create($text);
        $safeText->checkIsEmpty('Location');
        $safeText->checkMaxLength(self::MAX_LENGTH, 'Location');

        return new self($safeText->toString());
    }

    public function toString(): string
    {
        return $this->text;
    }
}
