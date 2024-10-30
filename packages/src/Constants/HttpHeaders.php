<?php

declare(strict_types=1);

namespace Tab\Packages\Constants;

final class HttpHeaders extends Constants
{
    public const HEADER_ACCESS_CONTROL_ALLOW_HEADERS = 'access-control-allow-headers';
    public const HEADER_ACCESS_CONTROL_ALLOW_METHODS = 'access-control-allow-methods';
    public const HEADER_ACCESS_CONTROL_ALLOW_ORIGIN = 'access-control-allow-origin';
    public const HEADER_ACCESS_CONTROL_MAX_AGE = 'access-control-max-age';
    public const HEADER_AUTHORIZATION = 'authorization';
    public const HEADER_CONTENT_LENGTH = 'content-length';
    public const HEADER_CONTENT_TYPE = 'content-type';
    public const HEADER_COOKIE = 'cookie';
    public const HEADER_HOST = 'host';
    public const HEADER_ORIGIN = 'origin';
    public const HEADER_SERVER_TIMING = 'server-timing';
    public const HEADER_SET_COOKIE = 'set-cookie';
    public const HEADER_USER_AGENT = 'user-agent';

    // Unofficial
    public const X_CACHE_TAGS = 'x-cache-tags';

    // OCS specific
    public const OCS_X_AUTH_KEY = 'x-auth-key';
    public const OCS_X_AUTH_TOKEN = 'x-auth-token';
    public const OCS_X_AUTH_TOKEN_EXPIRES = 'x-auth-token-expires';
    public const OCS_X_AUTH_USER = 'x-auth-user';
}
