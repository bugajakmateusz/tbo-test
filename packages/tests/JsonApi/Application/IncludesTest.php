<?php

declare(strict_types=1);

namespace Polsl\Packages\Tests\JsonApi\Application;

use Polsl\Packages\JsonApi\Application\Includes;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class IncludesTest extends UnitTestCase
{
    public function test_includes_can_be_constructed_from_array(): void
    {
        $includesArray = ['avatar', 'currentProject'];
        $includes = Includes::fromArray($includesArray);

        self::assertSame($includesArray, $includes->toArray());
    }
}
