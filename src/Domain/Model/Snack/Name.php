<?php

declare(strict_types=1);

namespace Tab\Domain\Model\Snack;

use Tab\Domain\SanitizedString;

final class Name
{
    public const MAX_LENGTH = 255;

    private function __construct(
        private readonly string $text,
    ) {}

    public static function fromString(
        string $text,
    ): self {
        $safeText = SanitizedString::create($text);
        $safeText->checkIsEmpty('Name');
        $safeText->checkMaxLength(
            self::MAX_LENGTH,
            'Name',
        );

        return new self(
            $safeText->toString(),
        );
    }

    public function toString(): string
    {
        return $this->text;
    }
}
