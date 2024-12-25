<?php

declare(strict_types=1);

namespace Polsl\Application\Service;

use Polsl\Domain\Model\Login\LoggedUser;

interface LoggedUserServiceInterface
{
    public function loggedUser(): LoggedUser;
}
