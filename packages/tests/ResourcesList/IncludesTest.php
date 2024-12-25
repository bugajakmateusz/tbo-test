<?php

declare(strict_types=1);

namespace Polsl\Packages\Tests\ResourcesList;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\ResourcesList\Includes;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class IncludesTest extends UnitTestCase
{
    public function test_includes_can_be_constructed_from_json_api_string(): void
    {
        $includesArray = Faker::wordsArray(4);
        $jsonApiString = \implode(',', $includesArray);

        $includes = Includes::fromJsonApiString($jsonApiString);

        self::assertSame($includesArray, $includes->toArray());
    }

    public function test_includes_can_be_constructed_with_empty_string(): void
    {
        $includes = Includes::fromJsonApiString('');

        self::assertCount(0, $includes->toArray());
    }
}
