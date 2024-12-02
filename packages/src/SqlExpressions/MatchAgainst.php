<?php

declare(strict_types=1);

namespace Tab\Packages\SqlExpressions;

final readonly class MatchAgainst
{
    public const MODE_BOOLEAN_ALL_WORDS = 'boolean_all_words';

    /**
     * @param string[] $fields
     * @param string[] $words
     */
    private function __construct(
        private array $fields,
        private array $words,
    ) {
    }

    /** @param string[] $fields */
    public static function createFromString(array $fields, string $searchTerm): self
    {
        $removeSpecialCharacters = \str_replace(
            [
                '+',
                '*',
                '-',
                '@',
                '<',
                '>',
                '(',
                ')',
            ],
            '',
            $searchTerm,
        );
        $normalizedSpaces = \preg_replace(
            '@\s+@',
            ' ',
            $removeSpecialCharacters,
        );
        if (null === $normalizedSpaces) {
            throw new \RuntimeException('Error occurred while creating normalized searched name.');
        }
        $trimmed = \trim($normalizedSpaces);
        if ('' === $trimmed) {
            throw new \RuntimeException('Normalized searched name cannot be empty.');
        }

        $words = \explode(' ', $trimmed);
        $filteredWords = \array_filter($words);

        return new self($fields, $filteredWords);
    }

    public function expresssionWithPlaceholder(string $mode, string $placeholder): string
    {
        return match ($mode) {
            self::MODE_BOOLEAN_ALL_WORDS => $this->booleanAllWordsExpressionWithPlaceholder($placeholder),
            default => throw new \RuntimeException("Mode '{$mode}' is not supported."),
        };
    }

    public function formatWords(string $mode): string
    {
        return match ($mode) {
            self::MODE_BOOLEAN_ALL_WORDS => $this->formatBooleanAllWords(),
            default => throw new \RuntimeException("Mode '{$mode}' is not supported."),
        };
    }

    private function booleanAllWordsExpressionWithPlaceholder(string $placeholder): string
    {
        $fields = \implode(', ', $this->fields);

        return "MATCH({$fields}) AGAINST (:{$placeholder} IN BOOLEAN MODE)";
    }

    private function formatBooleanAllWords(): string
    {
        $againstWords = \array_map(
            static fn (string $word): string => "+{$word}*",
            $this->words,
        );

        return \implode(' ', $againstWords);
    }
}
