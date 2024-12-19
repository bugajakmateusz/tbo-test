<?php

declare(strict_types=1);

namespace Tab\Packages\Collection;

final class CollectionException extends \Exception
{
    public static function emptyCollection(): self
    {
        return new self('Collection is empty.');
    }
}
