<?php

declare(strict_types=1);

namespace Polsl\Packages\Tests\SqlExpressions;

use Polsl\Packages\SqlExpressions\IfExpression;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class IfExpressionTest extends UnitTestCase
{
    public function test_if_expression_can_be_represented_as_string(): void
    {
        $ifExpression = new IfExpression(
            'user_id > 10',
            "'new'",
            "'old'",
        );

        self::assertSame("IF (user_id > 10, 'new', 'old')", $ifExpression->toString());
    }
}
