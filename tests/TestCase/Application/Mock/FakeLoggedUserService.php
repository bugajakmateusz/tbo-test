<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Mock;

use Tab\Application\Service\LoggedUserServiceInterface;
use Tab\Domain\Model\Login\LoggedUser;

final readonly class FakeLoggedUserService implements LoggedUserServiceInterface
{
    public function __construct(private LoggedUser $loggedUser) {}

    public function loggedUser(): LoggedUser
    {
        return $this->loggedUser;
    }
}
