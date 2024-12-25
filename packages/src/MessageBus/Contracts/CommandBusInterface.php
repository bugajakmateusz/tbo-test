<?php

declare(strict_types=1);

namespace Polsl\Packages\MessageBus\Contracts;

interface CommandBusInterface
{
    public function handle(object $command): void;
}
