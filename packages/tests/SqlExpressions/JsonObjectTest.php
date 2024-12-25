<?php

declare(strict_types=1);

namespace Polsl\Packages\Tests\SqlExpressions;

use Polsl\Packages\SqlExpressions\JsonObject;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class JsonObjectTest extends UnitTestCase
{
    public function test_json_object_can_be_presented_as_string(): void
    {
        $jsonObject = new JsonObject();
        $jsonObject
            ->addField('id', 'u.id')
            ->alias('user_data')
        ;

        self::assertSame("JSON_BUILD_OBJECT('id', u.id) AS user_data", $jsonObject->toString());
    }

    public function test_at_least_one_field_is_required_to_represent_as_string(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('At least one field is required.');

        (new JsonObject())->toString();
    }
}
