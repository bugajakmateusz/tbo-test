<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Fixtures;

use Tab\Packages\TestCase\Fixtures\Entity\Machine;
use Tab\Packages\TestCase\Fixtures\Entity\Snack;
use Tab\Packages\TestCase\Fixtures\Entity\User;

interface EntitiesLoaderInterface
{
    public const CUSTOM_ENTITIES = [
        User::class,
        Snack::class,
        Machine::class,
    ];

    public function load(object ...$objects): void;

    public function append(object ...$objects): void;

    public function purge(): void;
}
