<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\JsonApi\Application;

use Tab\Packages\JsonApi\Application\Includes;
use Tab\Packages\TestCase\UnitTestCase;

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
