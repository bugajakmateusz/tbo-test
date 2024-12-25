<?php

declare(strict_types=1);

namespace Polsl\Domain;

final readonly class SanitizedString
{
    private function __construct(private string $characters) {}

    public static function create(string $string, bool $encodeQuotes = true): self
    {
        /** @var string $replacedNullBytesAndTags */
        $replacedNullBytesAndTags = \preg_replace(
            '/\x00|<[^>]*>?/',
            '',
            $string,
        );
        /** @var string $quotesToEntities */
        $quotesToEntities = false === $encodeQuotes
            ? $replacedNullBytesAndTags
            : \str_replace(
                ["'", '"'],
                ['&#39;', '&#34;'],
                $replacedNullBytesAndTags,
            )
        ;
        $trimmed = \trim($quotesToEntities);

        return new self($trimmed);
    }

    public function toString(): string
    {
        return $this->characters;
    }

    public function checkIsEmpty(string $entityName): void
    {
        if ($this->isEmpty()) {
            throw new DomainException("{$entityName} cannot be empty.");
        }
    }

    public function checkMatchesPattern(string $regex, string $entityName): void
    {
        $regExMatch = \preg_match($regex, $this->toString());
        if (1 !== $regExMatch) {
            throw new DomainException("{$entityName} has not meet regex pattern.");
        }
    }

    public function checkMaxLength(int $maxLength, string $entityName): void
    {
        if ($maxLength < 1) {
            throw new DomainException('Maximum length cannot be lower than 1.');
        }

        $length = $this->length();
        if ($length > $maxLength) {
            throw new DomainException(
                "{$entityName} length cannot be higher than '{$maxLength}', '{$length}' passed.",
            );
        }
    }

    public function checkExactLength(int $length, string $entityName): void
    {
        if ($length < 1) {
            throw new DomainException('Exact length cannot be lower than 1.');
        }

        $passedLength = $this->length();
        if ($length !== $passedLength) {
            throw new DomainException(
                "{$entityName} length should be equal to '{$length}', '{$passedLength}' passed.",
            );
        }
    }

    private function isEmpty(): bool
    {
        return 0 === $this->length();
    }

    private function length(): int
    {
        $length = \grapheme_strlen($this->characters);
        if (false === \is_int($length)) {
            throw new DomainException('Unable to get length of string.');
        }

        return $length;
    }
}
