<?php

declare(strict_types=1);

namespace Polsl\Application\Query\LoggedUser;

use Polsl\Application\Service\LoggedUserServiceInterface;
use Polsl\Domain\Model\Login\LoggedUser as DomainLoggedUser;

final readonly class LoggedUserHandler
{
    public function __construct(private LoggedUserServiceInterface $loggedUser) {}

    public function __invoke(LoggedUser $loggedUser): DomainLoggedUser
    {
        return $this->loggedUser
            ->loggedUser()
        ;
    }
}
