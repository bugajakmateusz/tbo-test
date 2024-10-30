<?php

declare(strict_types=1);

namespace Tab\Packages\Constants;

final class Date extends Constants
{
    public const SQL_DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    public const SQL_DATE_TIME_WITH_MICROSECONDS_FORMAT = 'Y-m-d H:i:s.u';
    public const SQL_DATE_FORMAT = 'Y-m-d';
    public const YEAR_FORMAT = 'Y';
    public const YEAR_SHORT_FORMAT = 'y';
    public const ONE_MINUTE_IN_SECONDS = 60;
    public const ONE_HOUR_IN_SECONDS = 60 * self::ONE_MINUTE_IN_SECONDS;
    public const ONE_DAY_IN_SECONDS = 24 * self::ONE_HOUR_IN_SECONDS;
    public const ONE_WEEK_IN_SECONDS = 7 * self::ONE_DAY_IN_SECONDS;
}
