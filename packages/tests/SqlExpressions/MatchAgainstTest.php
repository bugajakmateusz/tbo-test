<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\SqlExpressions;

use Tab\Packages\Faker\Faker;
use Tab\Packages\SqlExpressions\MatchAgainst;
use Tab\Packages\TestCase\UnitTestCase;

/** @internal */
final class MatchAgainstTest extends UnitTestCase
{
    public function test_expression_with_placeholder(): void
    {
        // Arrange
        $fields = [
            Faker::word(),
            Faker::word(),
        ];
        $placeholder = Faker::word();
        $matchAgainst = MatchAgainst::createFromString($fields, Faker::words(2));
        $expectedFields = \implode(', ', $fields);
        $expectedExpression = "MATCH({$expectedFields}) AGAINST (:{$placeholder} IN BOOLEAN MODE)";

        // Act
        $expression = $matchAgainst->expresssionWithPlaceholder(
            MatchAgainst::MODE_BOOLEAN_ALL_WORDS,
            $placeholder,
        );

        // Assert
        self::assertSame($expectedExpression, $expression);
    }

    public function test_expression_with_placeholder_with_unsupported_mode(): void
    {
        // Arrange
        $mode = Faker::word();
        $matchAgainst = MatchAgainst::createFromString(
            [
                Faker::word(),
                Faker::word(),
            ],
            Faker::words(2),
        );

        // Expect
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Mode '{$mode}' is not supported.");

        // Act
        $matchAgainst->expresssionWithPlaceholder($mode, Faker::word());
    }

    public function test_format_words(): void
    {
        // Arrange
        $fields = [Faker::word()];
        $searchWord1 = Faker::word();
        $searchWord2 = Faker::word();
        $searchTerm = "+{$searchWord1}* () >{$searchWord2}-";
        $matchAgainst = MatchAgainst::createFromString($fields, $searchTerm);
        $expectedFormattedWords = "+{$searchWord1}* +{$searchWord2}*";

        // Act
        $formattedWords = $matchAgainst->formatWords(MatchAgainst::MODE_BOOLEAN_ALL_WORDS);

        // Assert
        self::assertSame($expectedFormattedWords, $formattedWords);
    }

    public function test_format_words_with_unsupported_mode(): void
    {
        // Arrange
        $matchAgainst = MatchAgainst::createFromString(
            [Faker::word()],
            Faker::words(3),
        );
        $mode = Faker::word();

        // Expect
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Mode '{$mode}' is not supported.");

        // Act
        $matchAgainst->formatWords($mode);
    }
}
