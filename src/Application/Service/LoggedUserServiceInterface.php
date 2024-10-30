<?php

declare(strict_types=1);

namespace Tab\Application\Service;

use Tab\Domain\Login\LoggedUser;

interface LoggedUserServiceInterface
{
    public function loggedUser(): LoggedUser;
}
