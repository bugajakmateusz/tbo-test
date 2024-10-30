<?php

declare(strict_types=1);

namespace Tab\Application\Query\LoggedUser;

use Tab\Application\Service\LoggedUserServiceInterface;
use Tab\Domain\Login\LoggedUser as DomainLoggedUser;

final readonly class LoggedUserHandler
{
    public function __construct(private LoggedUserServiceInterface $loggedUser)
    {
    }

    public function __invoke(LoggedUser $loggedUser): DomainLoggedUser
    {
        return $this->loggedUser
            ->loggedUser()
        ;
    }
}
