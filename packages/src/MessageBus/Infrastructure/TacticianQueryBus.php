<?php

declare(strict_types=1);

namespace Tab\Packages\MessageBus\Infrastructure;

use League\Tactician\CommandBus;
use Tab\Packages\MessageBus\Contracts\QueryBusInterface;

final readonly class TacticianQueryBus implements QueryBusInterface
{
    public function __construct(private CommandBus $queryBus) {}

    public function handle(object $query)
    {
        return $this->queryBus
            ->handle($query)
        ;
    }
}
