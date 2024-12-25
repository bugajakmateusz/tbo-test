<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase\Fixtures;

use Polsl\Packages\TestCase\Fixtures\Entity\Machine;
use Polsl\Packages\TestCase\Fixtures\Entity\MachineSnack;
use Polsl\Packages\TestCase\Fixtures\Entity\Snack;
use Polsl\Packages\TestCase\Fixtures\Entity\SnackPrice;
use Polsl\Packages\TestCase\Fixtures\Entity\User;
use Polsl\Packages\TestCase\Fixtures\Entity\WarehouseSnacks;

interface EntitiesLoaderInterface
{
    public const CUSTOM_ENTITIES = [
        User::class,
        Snack::class,
        Machine::class,
        MachineSnack::class,
        SnackPrice::class,
        WarehouseSnacks::class,
    ];

    public function load(object ...$objects): void;

    public function append(object ...$objects): void;

    public function purge(): void;
}
