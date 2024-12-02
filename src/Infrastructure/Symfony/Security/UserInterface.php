<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony\Security;

use Tab\Domain\Model\Login\LoggedUser;

interface UserInterface extends \Symfony\Component\Security\Core\User\UserInterface
{
    public function loggedUser(): LoggedUser;
}
