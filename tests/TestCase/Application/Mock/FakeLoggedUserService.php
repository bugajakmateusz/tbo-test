<?php

declare(strict_types=1);

namespace Polsl\Tests\TestCase\Application\Mock;

use Polsl\Application\Service\LoggedUserServiceInterface;
use Polsl\Domain\Model\Login\LoggedUser;

final readonly class FakeLoggedUserService implements LoggedUserServiceInterface
{
    public function __construct(private LoggedUser $loggedUser) {}

    public function loggedUser(): LoggedUser
    {
        return $this->loggedUser;
    }
}
