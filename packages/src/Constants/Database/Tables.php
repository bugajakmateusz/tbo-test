<?php

declare(strict_types=1);

namespace Tab\Packages\Constants\Database;

use Tab\Packages\Constants\Constants;

final class Tables extends Constants
{
    public const USERS = 'users';
    public const MACHINES = 'machines';
    public const SNACKS = 'snacks';
    public const MACHINE_SNACKS = 'machine_snacks';
    public const PRICES_HISTORY = 'prices_history';
    public const WAREHOUSE_PRICES_HISTORY = 'warehouse_prices_history';
    public const WAREHOUSE_SNACKS = 'warehouse_snacks';
    public const SNACK_SELLS = 'snack_sells';
}
