<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Client;

interface RequestInterface
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';
}
