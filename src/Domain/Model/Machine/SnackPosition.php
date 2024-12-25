<?php

declare(strict_types=1);

namespace Polsl\Domain\Model\Machine;

use Polsl\Domain\SanitizedString;

final class SnackPosition
{
    public const MAX_LENGTH = 3;

    private function __construct(private readonly string $text) {}

    public static function fromString(string $text): self
    {
        $safeText = SanitizedString::create($text);
        $safeText->checkIsEmpty('Position');
        $safeText->checkMaxLength(self::MAX_LENGTH, 'Position');

        return new self($safeText->toString());
    }

    public function toString(): string
    {
        return $this->text;
    }
}
