<?php

declare(strict_types=1);

namespace Polsl\Packages\Constants;

final class HttpMethods extends Constants
{
    public const DELETE = 'DELETE';
    public const GET = 'GET';
    public const PATCH = 'PATCH';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const OPTIONS = 'OPTIONS';

    // Unofficial
    public const PURGE = 'PURGE';
    public const PURGE_TAGS = 'PURGETAGS';
}
