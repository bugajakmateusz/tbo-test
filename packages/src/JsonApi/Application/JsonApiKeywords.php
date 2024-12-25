<?php

declare(strict_types=1);

namespace Polsl\Packages\JsonApi\Application;

final class JsonApiKeywords
{
    public const ID = 'id';
    public const TYPE = 'type';
    public const DATA = 'data';
    public const ATTRIBUTES = 'attributes';
    public const RELATIONSHIPS = 'relationships';
    public const PAGE = 'page';
    public const PAGE_SIZE = 'size';
    public const PAGE_NUMBER = 'number';
    public const FILTER = 'filter';
    public const INCLUDE = 'include';
    public const SORT = 'sort';
    public const FIELDS = 'fields';

    private function __construct()
    {
        // no-op
    }
}
