<?php

declare(strict_types=1);

namespace Tab\Packages\Constants;

final class Session extends Constants
{
    public const SESSION_LIFE_TIME = Date::ONE_DAY_IN_SECONDS * 31;
    public const SESSION_LIFE_TIME_OFFSET = '+' . self::SESSION_LIFE_TIME . ' seconds';
    public const GC_MAX_LIFE_TIME = Date::ONE_DAY_IN_SECONDS * 14;
    public const METADATA_UPDATE_THRESHOLD = 3 * Date::ONE_MINUTE_IN_SECONDS;
}
