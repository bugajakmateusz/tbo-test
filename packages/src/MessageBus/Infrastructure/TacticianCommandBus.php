<?php

declare(strict_types=1);

namespace Tab\Packages\MessageBus\Infrastructure;

use League\Tactician\CommandBus;
use Tab\Packages\MessageBus\Contracts\CommandBusInterface;

final readonly class TacticianCommandBus implements CommandBusInterface
{
    public function __construct(private CommandBus $commandBus) {}

    public function handle(object $command): void
    {
        $this->commandBus
            ->handle($command)
        ;
    }
}
